<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SavedSearch;
use App\Collection;
use App\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SavedSearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of saved searches for the authenticated user.
     * Saved searches are private to their owner and are not visible to other users.
     */
    public function index()
    {
        $user = Auth::user();
        return view('saved-searches.index', [
            'user' => $user,
        ]);
    }

    /**
     * Get saved searches data for DataTables.
     */
    public function data(Request $request)
    {
        try {
            $user = Auth::user();
            // Only return saved searches belonging to the authenticated user
            $savedSearches = SavedSearch::where('user_id', $user->id)
                ->with('collection')
                ->orderBy('created_at', 'desc')
                ->get();
            $data = [];
            foreach ($savedSearches as $search) {
                $query = $search->query ?? [];
                $searchText = $query['search_text'] ?? '';
                $filterCount = isset($query['meta_filters']) ? count($query['meta_filters']) : 0;

                // Build query summary
                $querySummary = '';
                if (!empty($searchText)) {
                    $querySummary .= '"' . e($searchText) . '"';
                }
                if ($filterCount > 0) {
                    $querySummary .= ($querySummary ? ' + ' : '') . $filterCount . ' ' . __('filter(s)');
                }
                if (empty($querySummary)) {
                    $querySummary = __('No filters');
                }

                $row = [
                    'id' => $search->id,
                    'name' => '<a href="' . route('saved-searches.apply', $search->id) . '">' . e($search->name) . '</a>',
                    'collection_name' => $search->collection ?
                        '<a href="/collection/' . $search->collection_id . '">' . e($search->collection->name) . '</a>' :
                        'N/A',
                    'query_summary' => $querySummary,
                    'preview_count' => $this->getPreviewCount($search),
                    'created_at' => $search->created_at ? $search->created_at->format('Y-m-d H:i') : 'N/A',
                    'actions' => $this->getActionButtons($search, $user),
                ];

                $data[] = $row;
            }

            return response()->json([
                'data' => $data,
                'recordsTotal' => count($data),
                'recordsFiltered' => count($data),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get preview count of documents matching the saved search.
     */
    private function getPreviewCount(SavedSearch $search)
    {
        try {
            $query = $search->query ?? [];
            $collection = $search->collection;
            
            if (!$collection) {
                return 0;
            }

            // Start with base query - count documents in collection
            $documentsQuery = Document::where('collection_id', $search->collection_id);

            // Apply search text if present (search in title)
            if (!empty($query['search_text'])) {
                $searchText = $query['search_text'];
                $documentsQuery->where('title', 'LIKE', '%' . $searchText . '%');
            }

            // Apply title filter if present
            if (!empty($query['title_filter'])) {
                $documentsQuery->where('title', 'LIKE', '%' . $query['title_filter'] . '%');
            }

            // Apply extension filter if present
            if (!empty($query['extension_filter'])) {
                $documentsQuery->where('type', $query['extension_filter']);
            }

            // For meta filters, we need to join with meta_field_values table
            if (!empty($query['meta_filters'])) {
                foreach ($query['meta_filters'] as $filter) {
                    $fieldId = $filter['field_id'] ?? null;
                    $value = $filter['value'] ?? null;
                    $operator = $filter['operator'] ?? '=';
                    
                    if ($fieldId && $value !== null) {
                        $documentsQuery->whereHas('meta', function($q) use ($fieldId, $value, $operator) {
                            $q->where('meta_field_id', $fieldId);
                            if ($operator === 'contains') {
                                $q->where('value', 'LIKE', '%' . $value . '%');
                            } elseif ($operator === 'between' && strpos($value, ' - ') !== false) {
                                $parts = explode(' - ', $value);
                                if (count($parts) == 2) {
                                    $q->whereBetween('value', [trim($parts[0]), trim($parts[1])]);
                                }
                            } else {
                                $q->where('value', $value);
                            }
                        });
                    }
                }
            }

            return $documentsQuery->count();
        } catch (\Exception $e) {
            \Log::error('SavedSearch preview count error: ' . $e->getMessage());
            return '-';
        }
    }

    /**
     * Generate action buttons for a saved search.
     */
    private function getActionButtons(SavedSearch $search, $user)
    {
        $buttons = '';
        
        // Apply search button
        $buttons .= '<a class="btn btn-success btn-link" title="' . __('Apply this search') . '" href="' . route('saved-searches.apply', $search->id) . '">';
        $buttons .= '<i class="material-icons">search</i>';
        $buttons .= '</a>';
        
        // Delete button (only for owner or admin)
        if ($search->user_id === $user->id) {
            $buttons .= '<button type="button" class="btn btn-danger btn-link delete-search-btn" data-search-id="' . $search->id . '" title="' . __('Delete saved search') . '">';
            $buttons .= '<i class="material-icons">delete</i>';
            $buttons .= '</button>';
        }
        
        return $buttons;
    }

    /**
     * Store a new saved search.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'collection_id' => 'required|exists:collections,id',
        ]);

        $user = Auth::user();
        $collectionId = $request->collection_id;

        // Build the query object from current session state
        $query = [];

        // Get search text from session
        $searchQuery = Session::get('search_query');
        if (!empty($searchQuery)) {
            $query['search_text'] = $searchQuery;
        }

        // Get meta filters from session
        $allMetaFilters = Session::get('meta_filters');
        if (!empty($allMetaFilters[$collectionId])) {
            $query['meta_filters'] = $allMetaFilters[$collectionId];
        }

        // Get title filter from session
        $titleFilter = Session::get('title_filter');
        if (!empty($titleFilter[$collectionId])) {
            $query['title_filter'] = $titleFilter[$collectionId];
        }

        // Get extension filter from session
        $extensionFilter = Session::get('extension_filter');
        if (!empty($extensionFilter[$collectionId])) {
            $query['extension_filter'] = $extensionFilter[$collectionId];
        }

        // Get search scope and fuzzy settings
        $fullTextScope = Session::get('full_text_scope');
        if (!empty($fullTextScope)) {
            $query['full_text_scope'] = $fullTextScope;
        }

        $fuzzy = Session::get('fuzzy');
        if (!empty($fuzzy)) {
            $query['fuzzy'] = $fuzzy;
        }

        $savedSearch = SavedSearch::create([
            'user_id' => $user->id,
            'collection_id' => $collectionId,
            'name' => $request->name,
            'query' => $query,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => __('Search saved successfully'),
                'saved_search' => $savedSearch,
            ]);
        }

        return redirect()->back()->with('alert-success', __('Search saved successfully'));
    }

    /**
     * Apply a saved search - redirect to collection with filters applied.
     */
    public function apply($id)
    {
        $user = Auth::user();
        $savedSearch = SavedSearch::findOrFail($id);
        
        // Only the owner may apply this saved search
        if ($savedSearch->user_id !== $user->id) {
            abort(403, __('You do not have permission to access this saved search.'));
        }

        $query = $savedSearch->query ?? [];
        $collectionId = $savedSearch->collection_id;

        // Clear existing filters for this collection first
        $allMetaFilters = Session::get('meta_filters', []);
        $allMetaFilters[$collectionId] = [];
        Session::put('meta_filters', $allMetaFilters);

        $titleFilter = Session::get('title_filter', []);
        unset($titleFilter[$collectionId]);
        Session::put('title_filter', $titleFilter);

        $extensionFilter = Session::get('extension_filter', []);
        unset($extensionFilter[$collectionId]);
        Session::put('extension_filter', $extensionFilter);

        // Apply saved search text
        if (!empty($query['search_text'])) {
            Session::put('search_query', $query['search_text']);
        } else {
            Session::forget('search_query');
        }

        // Apply meta filters
        if (!empty($query['meta_filters'])) {
            $allMetaFilters[$collectionId] = $query['meta_filters'];
            Session::put('meta_filters', $allMetaFilters);
        }

        // Apply title filter
        if (!empty($query['title_filter'])) {
            $titleFilter[$collectionId] = $query['title_filter'];
            Session::put('title_filter', $titleFilter);
        }

        // Apply extension filter
        if (!empty($query['extension_filter'])) {
            $extensionFilter[$collectionId] = $query['extension_filter'];
            Session::put('extension_filter', $extensionFilter);
        }

        // Apply search scope
        if (!empty($query['full_text_scope'])) {
            Session::put('full_text_scope', $query['full_text_scope']);
        }

        // Apply fuzzy setting
        if (isset($query['fuzzy'])) {
            Session::put('fuzzy', $query['fuzzy']);
        }

        // Redirect to collection page with search term if present
        $url = '/collection/' . $collectionId;
        if (!empty($query['search_text'])) {
            $url .= '?search_term=' . urlencode($query['search_text']);
        }

        return redirect($url)->with('alert-success', __('Saved search applied: ') . $savedSearch->name);
    }

    /**
     * Delete a saved search.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $savedSearch = SavedSearch::findOrFail($id);
        
        // Only the owner may delete this saved search
        if ($savedSearch->user_id !== $user->id) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('You do not have permission to delete this saved search.'),
                ], 403);
            }
            abort(403, __('You do not have permission to delete this saved search.'));
        }

        $savedSearch->delete();

        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => __('Saved search deleted successfully'),
            ]);
        }

        return redirect()->route('saved-searches.index')->with('alert-success', __('Saved search deleted successfully'));
    }

    /**
     * Get saved searches for a specific collection (AJAX).
     */
    public function forCollection($collectionId)
    {
        $user = Auth::user();
        $savedSearches = SavedSearch::where('user_id', $user->id)
            ->where('collection_id', $collectionId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'saved_searches' => $savedSearches->map(function($search) {
                return [
                    'id' => $search->id,
                    'name' => $search->name,
                    'created_at' => $search->created_at->format('Y-M-d'),
                ];
            }),
        ]);
    }
}
