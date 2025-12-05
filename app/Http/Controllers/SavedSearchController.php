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

                // Build query summary showing search text and explicit filter values
                $parts = [];
                if (!empty($searchText)) {
                    $parts[] = '"' . e($searchText) . '"';
                }

                // Meta filters: include readable label and value
                if (!empty($query['meta_filters']) && is_array($query['meta_filters'])) {
                    foreach ($query['meta_filters'] as $mf) {
                        $label = $mf['field_id'];
                        try {
                            $mfModel = \App\MetaField::find($mf['field_id']);
                            if ($mfModel && !empty($mfModel->label)) {
                                $label = $mfModel->label;
                            }
                        } catch (\Exception $e) {
                            // ignore and fall back to id
                        }
                        $value = isset($mf['value']) ? $mf['value'] : '';
                        $operator = isset($mf['operator']) ? $mf['operator'] : '';
                        $parts[] = e($label) . ' ' . e($operator) . ' "' . e($value) . '"';
                    }
                }

                // Title filter
                if (!empty($query['title_filter'])) {
                    $parts[] = __('Title contains') . ' "' . e($query['title_filter']) . '"';
                }

                // Extension / file type
                if (!empty($query['extension_filter'])) {
                    $ext = $query['extension_filter'];
                    // friendly mapping
                    $friendlyNames = [
                        'application/pdf' => 'PDF',
                        'application/msword' => 'DOC',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'DOCX',
                        'application/vnd.ms-excel' => 'XLS',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'XLSX',
                        'application/vnd.ms-powerpoint' => 'PPT',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'PPTX',
                        'image/jpeg' => 'JPEG',
                        'image/png' => 'PNG',
                        'image/gif' => 'GIF',
                        'text/plain' => 'TXT',
                        'text/csv' => 'CSV',
                        'application/zip' => 'ZIP',
                        'video/mp4' => 'MP4',
                        'audio/mpeg' => 'MP3'
                    ];
                    $label = isset($friendlyNames[$ext]) ? $friendlyNames[$ext] . ' (' . $ext . ')' : $ext;
                    $parts[] = __('File Type') . ': ' . e($label);
                }

                $querySummary = !empty($parts) ? implode(' + ', $parts) : __('No filters');

                $row = [
                    'id' => $search->id,
                    'name' => '<a href="' . route('saved-searches.apply', $search->id) . '">' . e($search->name) . '</a>',
                    'collection_name' => $search->collection ?
                        '<a href="/collection/' . $search->collection_id . '">' . e($search->collection->name) . '</a>' :
                        'N/A',
                    'query_summary' => $querySummary,
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

    // preview count removed — not required anymore

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

        // Build the query object from current request (preferred) and session state
        $query = [];

        // Prefer search_text sent in the request (from JS). Fall back to session.
        $searchTextReq = $request->input('search_text');
        if (!empty($searchTextReq)) {
            $query['search_text'] = $searchTextReq;
        } else {
            $searchQuery = Session::get('search_query');
            if (!empty($searchQuery)) {
                $query['search_text'] = $searchQuery;
            }
        }

        // Get meta filters from session (client doesn't send them in the save form)
        $allMetaFilters = Session::get('meta_filters');
        if (!empty($allMetaFilters[$collectionId])) {
            $query['meta_filters'] = $allMetaFilters[$collectionId];
        }

        // Get title filter from session
        $titleFilter = Session::get('title_filter');
        if (!empty($titleFilter[$collectionId])) {
            $query['title_filter'] = $titleFilter[$collectionId];
        }

        // Prefer extension_filter in request, fall back to session
        $extensionFilterReq = $request->input('extension_filter');
        if (!empty($extensionFilterReq)) {
            $query['extension_filter'] = $extensionFilterReq;
        } else {
            $extensionFilter = Session::get('extension_filter');
            if (!empty($extensionFilter[$collectionId])) {
                $query['extension_filter'] = $extensionFilter[$collectionId];
            }
        }

        // Get search scope and fuzzy settings from session
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
