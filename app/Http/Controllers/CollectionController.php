<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Collection;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Elastic\Elasticsearch\ClientBuilder;
use App\StorageTypes;
use App\SpideredDomain;
use App\DesiredUrl;
use App\UrlSuppression;
use App\CollectionMailbox;
use App\UserPermission;
use Rap2hpoutre\FastExcel\FastExcel;
use App\DocumentApproval;
use App\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Synonyms;
use App\SRTemplate;
use App\Traits\Search;

class CollectionController extends Controller
{
	use Search; // trait
	
    public function __construct()
    {
        //$this->middleware('collection_view');
    }

    public function index(){
        $collections = Collection::all();
        return view('collectionmanagement', ['collections'=>$collections, 'activePage'=>'Collections','titlePage'=>'Collections']);
    }

    public function add_edit_collection($collection_id){
        if($collection_id == 'new'){
            $collection = new \App\Collection();
        }
        else{
            $collection = \App\Collection::find($collection_id);
        }
	#$storage_types = StorageTypes::all();
	$storage_disks =  config('filesystems.disks');
        return view('collection-form', ['collection'=>$collection,'storage_disks'=>$storage_disks,'activePage'=>'Collection', 'titlePage'=>'Collection']);
    }


    public function list(Request $request){
		$collections = $this->userCollections(['VIEW_OWN','VIEW','MAINTAINER']);

        // Build a real-time document count map so we always show the true count
        // instead of the cached `document_count` column (which can go out of sync).
        $collectionIds = $collections->pluck('id')->toArray();
        $liveCounts = \App\Document::whereIn('collection_id', $collectionIds)
            ->whereNull('deleted_at')
            ->selectRaw('collection_id, COUNT(*) as cnt, COALESCE(SUM(size), 0) as total_size')
            ->groupBy('collection_id')
            ->get()
            ->keyBy('collection_id');

        $stats = [];
        foreach($collections as $collection){
            $live = $liveCounts->get($collection->id);
            $stats[$collection->id] = (object)[
                "cnt"  => $live ? (int)$live->cnt : 0,
                "size" => $live ? (int)$live->total_size : 0,
            ];
        }
        
        // Handle sorting
        $sort_by = $request->input('sort_by', 'name_asc'); // default to alphabetical ascending
        
        switch($sort_by) {
            case 'name_asc':
                $collections = $collections->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE);
                break;
            case 'name_desc':
                $collections = $collections->sortByDesc('name', SORT_NATURAL|SORT_FLAG_CASE);
                break;
            case 'size_asc':
                $collections = $collections->sortBy(function($collection) use ($stats) {
                    return isset($stats[$collection->id]) ? (int)$stats[$collection->id]->cnt : 0;
                });
                break;
            case 'size_desc':
                $collections = $collections->sortByDesc(function($collection) use ($stats) {
                    return isset($stats[$collection->id]) ? (int)$stats[$collection->id]->cnt : 0;
                });
                break;
            default:
                $collections = $collections->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE);
        }
        
        return view('collections', ['title'=>'Smart Repository','activePage'=>'collections','titlePage'=>'Collections','collections'=>$collections, 'stats'=>$stats, 'sort_by'=>$sort_by]);
    }

    public function save(Request $request){
            $request->validate([
                'collection_name' => 'required|string|max:255',
                'description' => 'required|string',
                'representative_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            ]);

         if(empty($request->input('collection_id'))){
            $c = new \App\Collection;
            $c->storage_drive = $request->input('storage_drive');
            $c->content_type = $request->input('content_type');
         }
         else{
            $c = \App\Collection::find($request->input('collection_id'));
         }
         $c->name = $request->input('collection_name');
         $c->description = $request->input('description');
         $c->type = empty($request->input('collection_type'))?'Public':$request->input('collection_type');
         $c->require_approval = $request->input('require_approval');
         // Store pdf_viewer in column_config JSON
         $col_config = json_decode($c->column_config) ?? new \stdClass();
         $col_config->pdf_viewer = $request->input('pdf_viewer', 'viewerjs');
         $c->column_config = json_encode($col_config);
         $c->user_id = Auth::user()->id;

            if ($request->hasFile('representative_image')) {
                $oldImagePath = $c->representative_image;
                $c->representative_image = $request->file('representative_image')->store('collection-images', 'public');
                if (!empty($oldImagePath) && Storage::disk('public')->exists($oldImagePath)) {
                     Storage::disk('public')->delete($oldImagePath);
                }
            } elseif ($request->input('remove_representative_image') == '1' && !empty($c->representative_image)) {
                if (Storage::disk('public')->exists($c->representative_image)) {
                    Storage::disk('public')->delete($c->representative_image);
                }
                $c->representative_image = null;
            }

         try{
            $c->save();
            Session::flash('alert-success', 'Collection saved successfully!');
         }
         catch(\Exception $e){
            Session::flash('alert-danger', $e->getMessage());
            return redirect('/admin/collectionmanagement');
         }
         // maintainer ID
         if(!empty($request->input('maintainer'))){
            $maintainer = \App\User::where('email', '=', $request->input('maintainer'))->first();
            $permission = \App\Permission::where('name','=','MAINTAINER')->first();
            $maintainer_permission = \App\UserPermission::where('collection_id','=',$c->id)->where('permission_id','=',$permission->id)->first();
            $maintainer_id = empty($maintainer->id)? null : $maintainer->id;
            if($maintainer_permission){
                $maintainer_permission->delete();
            }
            if($maintainer_id){
                $new_maintainer_permission = new \App\UserPermission();
                $new_maintainer_permission->permission_id = $permission->id;
                $new_maintainer_permission->collection_id = $c->id;
                $new_maintainer_permission->user_id = $maintainer_id;
                $new_maintainer_permission->save();
            }
            else{
                Session::flash('alert-warning', 'Maintainer was not found');
            }
         } 
         // create a storage dir for this collection if it does not exist
         if (!file_exists(storage_path().'/app/smartarchive_assets/'.$c->id.'/0')) {
            mkdir(storage_path().'/app/smartarchive_assets/'.$c->id.'/0', 0777, true);
         }
         return redirect('/admin/collectionmanagement');
    }

    public function collection($collection_id, Request $request){
        $collection = Collection::find($collection_id);
        return view('collection', ['collection'=>$collection, 
			'activePage'=>'collection','titlePage'=>'Collections', 
			'title'=>'Smart Repository']);
    }
    public function globalSearch(Request $request){
		$length = empty($request->length)? 10 : $request->length;
		$start = empty($request->start)? 0 : $request->start;
        $results = [];
        if(!empty($request->search['value'])){
            $request->merge(['return_format'=>'raw']);
            $results = $this->search($request);
        }
        return view('global-search', 
            ['results'=>empty($request->search['value'])?[]:json_decode($results),
            'search_term' => @$request->search['value'],
			'titlePage'=>'Global search', 
			'title'=>'Global search']);
    }

    public function collectionUsers($collection_id){
        $collection = Collection::find($collection_id);
        $user_permissions = \App\UserPermission::where('collection_id', '=', $collection_id)->get();
        //$user_permissions;
        $collection_users = array();
        foreach($user_permissions as $u_p){
            $collection_users[$u_p->user_id][] = $u_p;
        }
        return view('collection_users', ['collection'=>$collection, 'collection_users'=>$collection_users,'titlePage'=>'Collection Users','activePage'=>'Collection Users','title'=>'Collection Users']);
    }

    public function showCollectionUserForm($collection_id, $user_id=null){
        $user_permissions = array();
        $user = null;
	$has_approval=array();
	$has_approval = \App\Collection::where('id','=',$collection_id)->where('require_approval','=','1')->get();
        if(!empty($user_id)){
            $user = \App\User::find($user_id);
            $u_permissions = \App\UserPermission::where('user_id','=',$user_id)
                ->where('collection_id','=',$collection_id)->get();
            foreach($u_permissions as $u_p){
                $user_permissions['p'.$u_p->permission_id] = [1, $u_p->till_date];
            }
        }
        return view('collection-user-form', ['collection'=>\App\Collection::find($collection_id), 
            'user'=>$user, 
            'user_permissions'=>$user_permissions,
	    'collection_has_approval'=>$has_approval,
            'title'=>'Collection User Form',
	    'activePage'=>'Collection User Form',
	    'titlePage'=> 'Collection User Form'				
	]);
    }

    public function saveUser(Request $request){
	/*
	// WHY IS THIS VALIDATION NEEDED ? 
	// Just check if the user exists in the database
	$request->validate([
    	'user_id' => 'email:rfc,dns'
	]);
	 */
        $user = \App\User::where('email','=',$request->user_id)->first();
        // first delete all permissions on the collection
	if($user){
	    \App\UserPermission::where('collection_id','=',$request->collection_id)
            ->where('user_id','=',$user->id)->delete(); 
        $i = 0;
          foreach($request->permission as $p){
            $till_date = 'p_'.$p.'_till_date';
            $user_permission = new \App\UserPermission;
            $user_permission->user_id = $user->id;
            $user_permission->collection_id = $request->collection_id;
            $user_permission->permission_id = $p;
            $user_permission->till_date = $request->$till_date;
            $user_permission->save();
          }
	}
        return $this->collectionUsers($request->collection_id);
    }

    public function removeUser($collection_id, $user_id){
        \App\UserPermission::where('collection_id','=',$collection_id)
            ->where('user_id','=',$user_id)->delete(); 
        return $this->collectionUsers($collection_id);
    }







    public function addMetaFilter(Request $request){
        // set filters in session and return to the collection view 
        $meta_filters = Session::get('meta_filters');
        if(!empty($request->meta_value)){
            $meta_filters[$request->collection_id][] = array(
                'filter_id'=>\Uuid::generate()->string,
                'field_id'=>$request->meta_field,
                'operator'=>$request->operator,
                'value'=>$request->meta_value
            );
        }
        Session::put('meta_filters', $meta_filters);
        return redirect('/collection/'.$request->collection_id.'/metafilters');
    }

    /**
     * AJAX-friendly version of addMetaFilter that returns JSON instead of a redirect.
     * Used by the "Record Created" date filter on the classic collection view.
     * Replaces any existing created_at filter instead of stacking duplicates.
     */
    public function ajaxAddCreatedFilter(Request $request, $collection_id){
        $meta_filters = Session::get('meta_filters');
        if(!empty($request->meta_value)){
            // Remove any existing filters for the same field to avoid duplicates
            $field = $request->meta_field;
            if(!empty($meta_filters[$collection_id])){
                $meta_filters[$collection_id] = array_values(
                    array_filter($meta_filters[$collection_id], function($f) use($field){
                        return $f['field_id'] !== $field;
                    })
                );
            }
            $new_filter_id = \Uuid::generate()->string;
            $meta_filters[$collection_id][] = array(
                'filter_id'=>$new_filter_id,
                'field_id'=>$field,
                'operator'=>$request->operator,
                'value'=>$request->meta_value
            );
            Session::put('meta_filters', $meta_filters);
            return response()->json(['success' => true, 'filter_id' => $new_filter_id]);
        }
        return response()->json(['success' => false]);
    }

    public function replaceMetaFilter(Request $request){
/*
echo "Meta Value: ";print_r($request->meta_value); echo "<hr />";
echo "Meta Fields: "; print_r($request->meta_field); echo "<hr />";
echo "Meta Type: ";print_r($request->meta_type); echo "<hr />";
echo "Meta Operator: ";print_r($request->operator); echo "<hr />";
$j=0;
foreach($request->meta_field as $field){
foreach($request->meta_value as $key=>$value){
	echo "Key= ".$key."<br />";
		echo $j."<br />";
		if(count($value) > 0 && !empty($value[0])){
		print_r($request->meta_value[$key]);
		}
		echo "<br />";
		if($request->meta_field[$j] == $key && !empty($value[0])){
		echo "Meta Field: ".$request->meta_field[$j]."<br />";
		echo "Meta Type: ".$request->meta_type[$j]."<br />";
		echo "Operator: ".$request->operator[$j]."<br />";
		for($i=0;$i<count($value);$i++){
			echo $value[$i]."<br />";
		}
		}
}
	$j++;
	echo "<hr />";
}
exit;
*/
        // set filters in session and return to the collection view 
    $meta_filters = Session::get('meta_filters');
	$new_meta_filters = array();
	$multi_meta_field = $multi_meta_value = array();

        if(!empty($request->meta_value)){
			if($meta_filters && is_array(@$meta_filters[$request->collection_id])){
			foreach($meta_filters[$request->collection_id] as $m){
				if($m['field_id'] != $request->meta_field){
					$new_meta_filters[$request->collection_id][] = $m;
				}
			}
			}
			/*
			if($request->operator == 'between'){
				$range_parts = explode(' to ',ltrim(rtrim($request->meta_value)));
            			$new_meta_filters[$request->collection_id][] = array(
                			'filter_id'=>\Uuid::generate()->string,
                			'field_id'=>$request->meta_field,
		                	'operator'=>'>=',
		                	'value'=> $range_parts[0]
            			);
		            	$new_meta_filters[$request->collection_id][] = array(
               		 	'filter_id'=>\Uuid::generate()->string,
                			'field_id'=>$request->meta_field,
		                	'operator'=>'<=',
		                	'value'=> $range_parts[1]
            			);
			}
			else{
			*/
$j=0;
foreach($request->meta_field as $field){
foreach($request->meta_value as $key=>$value){
	if($request->meta_field[$j] == $key && !empty($value[0])){
	    	for($i=0;$i<count($value);$i++){
		if($request->operator[$j] == 'between'){
				$range_parts = explode(' to ',ltrim(rtrim($value[$i])));
				/*
				if($range_parts[0] == $range_parts[1]){
            			$new_meta_filters[$request->collection_id][] = array(
                			'filter_id'=>\Uuid::generate()->string,
                			'field_id'=>$request->meta_field[$j],
		                	'operator'=>'==',
		                	'value'=> $range_parts[0]
            			);
				}	
				else{
				*/
            			$new_meta_filters[$request->collection_id][] = array(
                			'filter_id'=>\Uuid::generate()->string,
                			'field_id'=>$request->meta_field[$j],
		                	'operator'=>'>=',
		                	'value'=> $range_parts[0]
            			);
		            	$new_meta_filters[$request->collection_id][] = array(
               		 	'filter_id'=>\Uuid::generate()->string,
                			'field_id'=>$request->meta_field[$j],
		                	'operator'=>'<=',
		                	'value'=> $range_parts[1]
            			);
				//}	
			}
		else{
	    		//for($i=0;$i<count($value);$i++){
				$new_meta_filters[$request->collection_id][] = array(
                     		'filter_id'=>\Uuid::generate()->string,
                     		'field_id'=>$request->meta_field[$j],
                     		'operator'=>$request->operator[$j],
                     		'value'=>$value[$i]
                 		);
	    		//}
		}##else for 'contains or matches' ends
	    	}
	}
} // foreach $request->meta_value ends
$j++;
} // foreach $request->meta_fields ends
//exit;
        }
	//print_r($new_meta_filters); exit;
        Session::put('meta_filters', $new_meta_filters);
        return redirect('/collection/'.$request->collection_id);
    }

    public function setSearchScope(Request $request){
        Session::put('full_text_scope', $request->input('scope'));
        //Log::debug('Scope: '.$request->input('scope'));
    }

    public function setFuzzySearch(Request $request){
        $fuzzy = ($request->input('fuzzy') == 'true') ? 1 : 0;
        Session::put('fuzzy', $fuzzy);
        //Log::debug('Fuzzy: '.$fuzzy);
    }

	public function replaceTitleFilter(Request $request){
		$title_filter = Session::get('title_filter');
		$title_filter[$request->collection_id] = $request->title_filter;
		Session::put('title_filter', $title_filter);
        return redirect('/collection/'.$request->collection_id);
	}
    
    /**
     * Replace extension filter for a collection
     */
    public function replaceExtensionFilter(Request $request){
        $extension_filter = Session::get('extension_filter');
        $extension_filter[$request->collection_id] = $request->extension_filter;
        Session::put('extension_filter', $extension_filter);
        return redirect('/collection/'.$request->collection_id);
    }
    
    /**
     * Get unique extensions for a collection (AJAX endpoint)
     */
    public function getCollectionExtensions($collection_id){
        $extensions = Document::where('collection_id', $collection_id)
            ->whereNull('deleted_at')
            ->distinct()
            ->pluck('type')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
        return response()->json(['extensions' => $extensions]);
    }
    
    public function metaInformation($collection_id, $meta_field_id=null){
        $collection = \App\Collection::find($collection_id);
        if(empty($meta_field_id)){
            $edit_field = new \App\MetaField;
        }
        else{
            $edit_field = \App\MetaField::find($meta_field_id);
        }
        $meta_fields = $collection->meta_fields;
	$permissions = \App\Permission::all();
        return view('metainformation', ['collection'=>$collection, 'permissions'=>$permissions,
                'edit_field'=>$edit_field, 
                'meta_fields'=>$meta_fields,
		'activePage' =>'Collections Meta Data',
		'titlePage'=>'Collections Metadata Fields']);
    }

    public function saveMeta(Request $request){
        $collection = \App\Collection::find($request->input('collection_id'));
        if(empty($request->input('meta_field_id'))){
            $meta_field = new \App\MetaField;
        }
        else{
            $meta_field = \App\MetaField::find($request->input('meta_field_id'));
        }
        $meta_field->collection_id = $request->input('collection_id');
        $meta_field->label = $request->input('label');
        $meta_field->placeholder = $request->input('placeholder');
	if(!empty($request->input('available_to'))){
	$meta_field->available_to = implode(",",$request->input('available_to'));
	}
        $meta_field->type = $request->input('type');
	if($request->type == 'TaxonomyTree'){
        	$meta_field->options = $request->input('treeoptions');
	}
	elseif(preg_match('/Select/i',$request->type)){
        	$meta_field->options = $request->input('options');
	}
    else{
        	$meta_field->options = '';
    }
        $meta_field->display_order = $request->input('display_order');
        $meta_field->results_display_order = $request->input('results_display_order');
        $meta_field->is_required = $request->input('is_required');
        $meta_field->is_filter = $request->input('is_filter');
        $meta_field->with_rich_text_editor = $request->input('with_rich_text_editor');
		// extra attributes
			$extra_attributes = empty($meta_field->extra_attaibutes) ? [] :json_decode($meta_field->extra_attaibutes); 
			$extra_attributes['width_on_info_page'] = $request->input('width_on_info_page');
			$extra_attributes['numeric_min_value'] = $request->input('numeric_min_value');
			$extra_attributes['numeric_max_value'] = $request->input('numeric_max_value');
			$extra_attributes['min_year_setting'] = $request->input('min_year_setting');
			$extra_attributes['max_year_setting'] = $request->input('max_year_setting');
			$extra_attributes['show_on_details_page'] = $request->input('show_on_details_page');
			$extra_attributes['show_parents'] = $request->input('show_parents');
			$extra_attributes['results_classname'] = $request->input('results_classname');
			$extra_attributes['filter_width_on_collection_page'] = $request->input('filter_width_on_collection_page');
			$meta_field->extra_attributes = json_encode($extra_attributes);

        $meta_field->save();

        // Handle series configuration attached to this meta field
        if($request->input('auto_generate_series')){
            $series = \App\MetaFieldSeries::where('meta_field_id', $meta_field->id)->first();
            if(!$series){
                $series = new \App\MetaFieldSeries();
                $series->meta_field_id = $meta_field->id;
            }
            $series->prefix = $request->input('series_prefix');
            $series->date_format = $request->input('series_date_format');
            $series->next_sequence = intval($request->input('series_next_sequence')) ?: 1;
            $series->pad_length = intval($request->input('series_pad_length')) ?: 0;
            $series->save();
        }
        else{
            // remove existing series if auto-generate not set
            \App\MetaFieldSeries::where('meta_field_id', $meta_field->id)->delete();
        }

        return $this->metaInformation($request->input('collection_id'));
    }

    public function deleteMetaField($collection_id,$meta_field_id){
        $meta_field = \App\MetaField::find($meta_field_id);
        $collection_id = $meta_field->collection_id;
        $meta_field->delete();
        return redirect('/collection/'.$collection_id.'/meta');
    }

    public function metaFiltersForm($collection_id){
        $collection = \App\Collection::find($collection_id);
        return view('metasearch', ['collection'=>$collection, 
            'activePage'=>'Set Meta Filters',
            //'titlePage'=>'Set Meta Filters',
            'title'=>'Smart Repository'
            ]
            );
    }
    
    public function removeMetaFilter($collection_id, $filter_id){
        $all_meta_filters = Session::get('meta_filters');
        $new_collection_filters = array();
        foreach($all_meta_filters[$collection_id] as $mf){
            if($mf['filter_id'] == $filter_id) continue;
            $new_collection_filters[] = $mf;
        }
        $all_meta_filters[$collection_id] = $new_collection_filters;
        Session::put('meta_filters', $all_meta_filters);
        return redirect('/collection/'.$collection_id);
    }

	public function removeTitleFilter($collection_id){
		$title_filter = Session::get('title_filter');
		$title_filter[$collection_id] = null;
        Session::put('title_filter', $title_filter);
        return redirect('/collection/'.$collection_id);
	}

    /**
     * Remove extension filter for a collection
     */
    public function removeExtensionFilter($collection_id){
        $extension_filter = Session::get('extension_filter');
        $extension_filter[$collection_id] = null;
        Session::put('extension_filter', $extension_filter);
        return redirect('/collection/'.$collection_id);
    }

    public function removeAllMetaFilters($collection_id){
        $all_meta_filters = Session::get('meta_filters');
        $all_meta_filters[$collection_id] = null;
        Session::put('meta_filters', $all_meta_filters);
        return redirect('/collection/'.$collection_id);
    }

	public function removeAllFilters($collection_id){
		$title_filter = Session::get('title_filter');
		$title_filter[$collection_id] = null;
        Session::put('title_filter', $title_filter);
        
        $extension_filter = Session::get('extension_filter');
        $extension_filter[$collection_id] = null;
        Session::put('extension_filter', $extension_filter);
        
        $all_meta_filters = Session::get('meta_filters');
        $all_meta_filters[$collection_id] = null;
        Session::put('meta_filters', $all_meta_filters);
        return redirect('/collection/'.$collection_id);
	}

	/**
     * AJAX method to set extension filter without page refresh
     */
    public function ajaxSetExtensionFilter(Request $request){
        $extension_filter = Session::get('extension_filter');
        $extension_filter[$request->collection_id] = $request->extension_filter;
        Session::put('extension_filter', $extension_filter);
        return response()->json(['success' => true, 'message' => 'Filter applied successfully']);
    }

    /**
     * AJAX method to clear all filters without page refresh
     */
    /**
     * Returns date-grouped document counts for current filtered results.
     * Used by the classic view to display upload-date breakdown chips.
     */
    public function dateFacets(Request $request, $collection_id){
        $request->merge(['collection_id' => $collection_id]);
        $documents = \App\Document::where('collection_id', $collection_id);
        $documents = $this->getMetaFilteredDocuments($request, $documents);
        $documents = $this->getTitleFilteredDocuments($request, $documents);
        $documents = $this->getExtensionFilteredDocuments($request, $documents);
        $facets = $documents
            ->selectRaw('DATE(created_at) as upload_date, COUNT(*) as doc_count')
            ->groupBy('upload_date')
            ->orderBy('upload_date', 'desc')
            ->get();
        $result = [];
        foreach($facets as $f){
            $result[] = [
                'formatted' => date('d-m-Y', strtotime($f->upload_date)),
                'raw_date'  => $f->upload_date,
                'count'     => (int)$f->doc_count
            ];
        }
        return response()->json($result);
    }

    /**
     * AJAX: adds a created_at != date exclusion filter to session.
     */
    public function ajaxExcludeDate(Request $request, $collection_id){
        $date = $request->date;
        if(!$date) return response()->json(['success' => false]);
        $meta_filters = Session::get('meta_filters') ?? [];
        if(empty($meta_filters[$collection_id])) $meta_filters[$collection_id] = [];
        $filter_id = \Uuid::generate()->string;
        $meta_filters[$collection_id][] = [
            'filter_id' => $filter_id,
            'field_id'  => 'created_at',
            'operator'  => '!=',
            'value'     => $date
        ];
        Session::put('meta_filters', $meta_filters);
        return response()->json(['success' => true, 'filter_id' => $filter_id]);
    }

    /**
     * AJAX method to remove a single meta filter by filter_id without page refresh.
     */
    public function ajaxRemoveFilter($collection_id, $filter_id){
        $all_meta_filters = Session::get('meta_filters');
        if(!empty($all_meta_filters[$collection_id])){
            $all_meta_filters[$collection_id] = array_values(
                array_filter($all_meta_filters[$collection_id], function($f) use($filter_id){
                    return $f['filter_id'] !== $filter_id;
                })
            );
            Session::put('meta_filters', $all_meta_filters);
        }
        return response()->json(['success' => true]);
    }

    public function ajaxClearAllFilters($collection_id){
        $title_filter = Session::get('title_filter');
        $title_filter[$collection_id] = null;
        Session::put('title_filter', $title_filter);
        
        $extension_filter = Session::get('extension_filter');
        $extension_filter[$collection_id] = null;
        Session::put('extension_filter', $extension_filter);
        
        $all_meta_filters = Session::get('meta_filters');
        $all_meta_filters[$collection_id] = null;
        Session::put('meta_filters', $all_meta_filters);
        
        return response()->json(['success' => true, 'message' => 'All filters cleared successfully']);
    }


    public function deleteCollection(Request $request){
        $collection = \App\Collection::find($request->collection_id);

    	if ($collection != null) {
	if(!empty($request->delete_captcha) &&
                $request->delete_captcha == $request->delete_captcha){
       	 	if($collection->delete()){
            	Session::flash('alert-success', 'Collection deleted successfully!');
       	 	return redirect('/admin/collectionmanagement');
		}
        }
        else{
                Session::flash('alert-danger', 'Please fill Captcha');
                return redirect('/admin/collectionmanagement');
        }
    	}

    }

    private function datatableFormatResultsEmbedded($data){
        $documents = $data['documents'];
        $request = $data['request'];

	$collection = \App\Collection::find($request->collection_id);

        $results_data = array();

        foreach($documents as $d){
	    $title = $d->title.'<br />'. substr($d->text_content, 0, 100).' ...';
            $results_data[] = array(
                //'type' => array('display'=>'<a href="/collection/'.$request->collection_id.'/document/'.$d->id.'/details"><img class="file-icon" src="'.env('APP_URL').'/i/file-types/'.$d->icon().'.png" style="width:20px;"/></a>', 'filetype'=>$d->icon()),
                'type' => array('display'=>'<img class="file-icon" src="'.env('APP_URL').'/i/file-types/'.$d->icon().'.png" style="width:20px;"/>', 'filetype'=>$d->icon()),
                'title' => '<a href="'.env('APP_URL').'/collection/'.$request->collection_id.'/document/'.$d->id.'/details" target="_blank">'.$title.'</a>',
                'size' => array('display'=>$d->human_filesize(), 'bytes'=>$d->size),
                'updated_at' => array('display'=>date('d-m-Y', strtotime($d->updated_at)), 'updated_date'=>$d->updated_at),
                );
        }
        return $results_data;
    }

    public function collectionUrls($collection_id){
        if($collection_id == 'new'){
            $collection = new \App\Collection();
			$spidered_domains = null;
			$desired = null;
			$excluded = null;
        }
        else{
            $collection = \App\Collection::find($collection_id);
			$spidered_domains = \App\SpideredDomain::where('collection_id', $collection_id)->get();
			$desired = \App\DesiredUrl::where('collection_id', $collection_id)->get();
			$excluded = \App\UrlSuppression::where('collection_id', $collection_id)->get();
        }
        return view('save_exclude_sites', 
			['collection'=>$collection,
			'activePage'=>'Collection', 
			'titlePage'=>'Collection',
			'spidered_domains'=>$spidered_domains,
			'desired'=>$desired,
			'excluded'=>$excluded,
			]);
    }

    public function saveCollectionUrls(Request $request){
	$domain_link = $request->spidered_domain;
	$existing_domains = array();
	$sd = SpideredDomain::all();
	foreach($sd as $domain){
		$existing_domains[] = $domain->web_address;
	}		
	if(!in_array($domain_link,$existing_domains)){
		$sd = new \App\SpideredDomain;
		$sd->collection_id = $request->input('collection_id');
		$sd->web_address = $domain_link;
       	try{
			if(!empty($domain_link)){
		   		$sd->save();
				$domain_id = $sd->id;
			}
			if(!empty($request->input('save_urls'))){
				$this->saveDesiredUrls($request);		
			}
			if(!empty($request->input('exclude_urls'))){
				$this->excludeUrls($request);		
			}
           	Session::flash('alert-success', 'Site URLs saved successfully!');
           	return redirect('/collection/'.$request->collection_id.'/save_exclude_sites');
        }
       	catch(\Exception $e){
           	Session::flash('alert-danger', $e->getMessage());
           	return redirect('/collection/'.$request->collection_id.'/save_exclude_sites');
       	}
	} ##if ends for existing domains check
	else{
            	Session::flash('alert-danger', 'Domain already spidered.');
            	return redirect('/collection/'.$request->collection_id.'/save_exclude_sites');
	}
/*
use App\SpideredDomain;
use App\DesiredUrl;
use App\UrlSuppression;
*/
    }

    public function saveDesiredUrls($request){
	$url_start_patterns = explode("\n",$request->input('save_urls'));
	$collection_id = $request->input('collection_id');
#DB::enableQueryLog();
	foreach($url_start_patterns as $url){
		if(empty($url)) continue;
		$su = new \App\DesiredUrl;	
		$su->collection_id = $collection_id;
		$su->url_start_pattern = rtrim(ltrim($url));
    	$su->save();
	}
    }	

    public function excludeUrls($request){
	$url_start_patterns = explode("\n",$request->input('exclude_urls'));
	$collection_id = $request->input('collection_id');
	foreach($url_start_patterns as $url){
		if(empty($url)) continue;
		$su = new \App\UrlSuppression;
		$su->collection_id = $collection_id;
		$su->url_start_pattern = rtrim(ltrim($url));
    	$su->save();
	}
    }	

	public function removeSpideredDomain(Request $request){
		\App\SpideredDomain::find($request->domain_id)->delete();
		return redirect('/collection/'.$request->collection_id.'/save_exclude_sites');
	}
	public function removeDesiredLink(Request $request){
		\App\DesiredUrl::find($request->link_id)->delete();
		return redirect('/collection/'.$request->collection_id.'/save_exclude_sites');
	}
	public function removeExcludedLink(Request $request){
		\App\UrlSuppression::find($request->link_id)->delete();
		return redirect('/collection/'.$request->collection_id.'/save_exclude_sites');
	}

	// column-config
	public function showSettingsForm(Request $request){
		$collection = Collection::find($request->collection_id);
		$roles = Role::all();
		$search_result_template = SRTemplate::where('collection_id', $collection->id)
			->where('template_type', 'search_result')->first();
		$details_page_template = SRTemplate::where('collection_id', $collection->id)
			->where('template_type', 'details_page')->first();
        return view('collection-settings', [
			'collection' => $collection,
			'roles' => $roles,
			'mailbox' => CollectionMailbox::where('collection_id', $collection->id)->first(),
			'search_result_template' => $search_result_template,
			'details_page_template' => $details_page_template,
		]);
	}
	
	public function saveSettings(Request $request){
		$collection = Collection::find($request->collection_id);
        $col_config = $request->except(['search_result_template_html', 'details_page_template_html', 'approval_status_labels_text', 'approval_checklist_labels_text']);
        $col_config['require_two_factor'] = $request->boolean('require_two_factor') ? 1 : 0;
    $col_config['autoscroll_interval'] = max(1, (int) $request->input('autoscroll_interval', 50));
    $col_config['autoscroll_speed'] = max(1, (int) $request->input('autoscroll_speed', 1));

        $approvalRoles = $request->input('approved_by', []);
        $approvalStatusLabelsText = trim((string) $request->input('approval_status_labels_text', ''));
        if ($approvalStatusLabelsText === '') {
            unset($col_config['approval_status_labels']);
        } else {
            $approvalStatusLabels = preg_split('/\r\n|\r|\n/', $approvalStatusLabelsText);
            $approvalStatusLabels = array_map('trim', $approvalStatusLabels);

            if (count($approvalStatusLabels) !== count($approvalRoles)) {
                Session::flash('alert-danger', 'The number of approval status labels must match the number of workflow roles.');
                return redirect()->back()->withInput();
            }

            if (in_array('', $approvalStatusLabels, true)) {
                Session::flash('alert-danger', 'Approval status labels cannot be empty when labels are provided.');
                return redirect()->back()->withInput();
            }

            $col_config['approval_status_labels'] = array_values($approvalStatusLabels);
        }

        $approvalChecklistLabelsText = trim((string) $request->input('approval_checklist_labels_text', ''));
        if ($approvalChecklistLabelsText === '') {
            unset($col_config['approval_checklist_labels']);
        } else {
            $approvalChecklistStageLines = preg_split('/\r\n|\r|\n/', $approvalChecklistLabelsText);
            $approvalChecklistLabels = [];

            foreach ($approvalChecklistStageLines as $stageLine) {
                $stageLine = trim((string) $stageLine);
                if ($stageLine === '') {
                    $approvalChecklistLabels[] = [];
                    continue;
                }

                $stageLabels = preg_split('/\s*\|\s*/', $stageLine);
                $stageLabels = array_values(array_filter(array_map('trim', $stageLabels), function ($label) {
                    return $label !== '';
                }));

                $approvalChecklistLabels[] = $stageLabels;
            }

            if (count($approvalChecklistLabels) !== count($approvalRoles)) {
                Session::flash('alert-danger', 'The number of checklist stages must match the number of workflow roles.');
                return redirect()->back()->withInput();
            }

            $col_config['approval_checklist_labels'] = array_values($approvalChecklistLabels);
        }

		$collection->column_config = json_encode($col_config);
		$collection->save();

		// Save search result template
		$sr_template = SRTemplate::where('collection_id', $collection->id)
			->where('template_type', 'search_result')->first();
		if(empty($sr_template)){
			$sr_template = new SRTemplate();
			$sr_template->collection_id = $collection->id;
			$sr_template->template_type = 'search_result';
			$sr_template->template_name = 'Search Result - '.$collection->name;
		}
		$sr_template->html_code = $request->input('search_result_template_html', '');
		$sr_template->save();

		// Save details page template
		$details_template = SRTemplate::where('collection_id', $collection->id)
			->where('template_type', 'details_page')->first();
		if(empty($details_template)){
			$details_template = new SRTemplate();
			$details_template->collection_id = $collection->id;
			$details_template->template_type = 'details_page';
			$details_template->template_name = 'Details Page - '.$collection->name;
		}
		$details_template->html_code = $request->input('details_page_template_html', '');
		$details_template->save();
		// configuration of mapping of mailbox
		$mailbox = CollectionMailbox::where('collection_id', $collection->id)->first();
		if(empty($mailbox)){
			$mailbox = new CollectionMailbox;
		}
		if(!empty($request->input('email_address'))){
			$cred_array = array(
				'server_type'=>'IMAP',
				'server_address'=>$request->input('imap_server'),
				'server_port'=>$request->input('server_port'),
				'security'=>$request->input('security'),
				'username'=>$request->input('username'),
				'password'=>$request->input('password'),
			);
			$mailbox->address = $request->input('email_address');
			$mailbox->collection_id = $request->input('collection_id');
			$mailbox->credentials = json_encode($cred_array);
			$mailbox->save();
		}
		return redirect('/collection/'.$collection->id);
	}

	public function export(Request $request, $collection_id){
		$documents = \App\Document::where('collection_id', $collection_id);
		$documents = $this->getTitleFilteredDocuments($request, $documents);
		$documents = $this->getMetaFilteredDocuments($request, $documents);

		$collection = \App\Collection::find($collection_id);
		$filename = $collection->name;
		$meta_fields = $collection->meta_fields;
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename={$filename}.tsv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo 'id'."\t".'title'."\t";
		foreach($meta_fields as $m){
			echo $m->label."\t";
		}
        echo "Related document IDs";
		echo "\n";
		$documents->chunk(100, function($documents){
		foreach($documents as $d){
			// remove tab spaces from the title, if present
			echo $d->id."\t".preg_replace("/\t/", " ", $d->title)."\t";
			$meta_fields = $d->collection->meta_fields;
			foreach($meta_fields as $m){
				if(!empty($d->meta_value($m->id))){
					echo preg_replace("/\s/"," ", $d->meta_value($m->id));
				}
				else{
					echo " - ";
				}
				echo "\t";
			}
            // Related documents
            $related_docs = $d->related_documents->pluck('related_document_id');
            //echo implode("|", $related_docs->all());
            echo "x";
			echo "\n";
		}
		});// chunking ends
		exit;
	}

	public function showChildCollectionForm(Request $req){
		if($req->child_collection_id == 'new'){
			$collection = new Collection;
		}
		else{
			$collection = Collection::find($req->child_collection_id);
		}
		return view('child-collection-form',['collection'=>$collection]);
	}

	public function saveChildCollection(Request $req){
		$collection = Collection::find($req->collection_id);
		$child = $collection->replicate();
		$child->name = $req->collection_name;
		$child->description = $req->description;
		$child->parent_id = $collection->id;
		$child->column_config = null;
		$child->save();
		// clone user permissions on the child collection
		$user_permissions = UserPermission::where('collection_id', $collection->id)->get();
		foreach($user_permissions as $u_p){
			$u_p_new = $u_p->replicate();
			$u_p_new->collection_id = $child->id;
			$u_p_new->save();
		}
		return redirect('/collection/'.$req->collection_id);
	}

    public function deleteSubCollection(Request $request){
                $collection = \App\Collection::find($request->collection_id);
                $parent_collection_id = $collection->parent_id;

                if ($collection != null) {
                        if(!empty($request->delete_subcollection_captcha) &&
                        $request->delete_subcollection_captcha == $request->hidden_subcollection_captcha){
                        if($collection->delete()){
                                Session::flash('alert-success', 'Collection deleted successfully!');
                                return redirect('/collection/'.$parent_collection_id);
                        }
                        }
                        else{
                                Session::flash('alert-danger', 'Please fill Captcha');
                                return redirect('/collection/'.$parent_collection_id);
                        }
                }
    }//delete sub collection funcion ends


	public function exportXlsx(Request $request, $collection_id){
		//echo $collection_id; exit;
		$list = $meta_details = [];
		$filename = '';
        $request->merge(['return_format'=>'raw', 
                    'length'=>10000, 
                    'collection_id'=>$collection_id,
                    ]);
        $search_data = json_decode($this->search($request));
        $doc_ar = []; 
        foreach($search_data->data as $d){
            $doc_ar[] = $d->id;
        }
		$documents = Document::whereIn('id',$doc_ar);
        
		$collection = \App\Collection::find($collection_id);
		$filename = $collection->name;
		$meta_fields = $collection->meta_fields;
		$new_list  = $new_meta_details = [];
		
		$documents->chunk(100, function($documents) use (&$new_list){ // chunking starts
            foreach($documents as $d){
  			$list = ['ID'=>$d->id,'Title'=>$d->title,'Path'=>$d->path];
			$meta_fields = $d->collection->meta_fields;
			$meta_details=[];
			foreach($meta_fields as $m){
				if($m->type=='MultiSelect' || $m->type == 'Select'){
					$details_select = trim($d->meta_value($m->id),'[]');
					$details_select = preg_replace('/"/',"",$details_select);
					//$meta_details = [$m->label => $d->meta_value($m->id)];
					$meta_details = [$m->label => $details_select];
				}
				else{
                    $extra_attributes = json_decode($m->extra_attributes);
                    $show_parents = empty($extra_attributes->show_parents)?false:true;
                    if($m->type == 'TaxonomyTree' && $show_parents){
					    $meta_details = [$m->label => html_entity_decode($d->meta_value($m->id, false, $show_parents))];
                    }
                    else{
					    $meta_details = [$m->label => html_entity_decode($d->meta_value($m->id))];
                    }
				}
				$list = array_merge($list,$meta_details);
                $related_doc_ids = $d->related_documents->pluck('related_document_id');
                $list['Related document IDs'] = implode("|", $related_doc_ids->all());
		}
			$new_list[] = array_merge($list,$meta_details);
		}
		});// chunking ends
		//exit;
		return (new FastExcel($new_list))
    			->download($filename.'.xlsx');
	}

    public function setDocSort(Request $req){
        Session::put('doc_sort', $req->collection_id.':'.$req->sort_by);
        return true;
    }
    //Language Selection
    public function selectLanguage(Request $request){
        $referer = $request->header('referer');
        //echo $request->sr_lang; echo $referer; exit;
        // set language in session
        if(!empty($request->sr_lang)){
        Session::put('sr_lang', $request->sr_lang);
        //echo $lang = Session::get('sr_lang')."skk"; exit;
        }
        else{
        Session::put('sr_lang', 'en');
        }
        return redirect($referer);
    }
//Class Ends
}
