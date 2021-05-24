<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Collection;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Elasticsearch\ClientBuilder;
use App\StorageTypes;
use App\SpideredDomain;
use App\DesiredUrl;
use App\UrlSuppression;
use App\CollectionMailbox;

class CollectionController extends Controller
{
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

    public function userCollections(){
        /*
         Get all public collections 
         plus collections to which the current user has access.
         Access to members-only collection is determined by db_table:user_permissions 
        */
        $user_collections = array();
        $user_permissions = empty(Auth::user()) ? array() : Auth::user()->accessPermissions();
        foreach($user_permissions as $u_p){
            if(!in_array($u_p->collection_id, $user_collections)){
                array_push($user_collections, $u_p->collection_id);
            }
        }
        $collections = Collection::whereIn('id', $user_collections)->orWhere('type','=','Public')->get();
	return $collections;
    }

    public function list(){
	$collections = $this->userCollections();
        return view('collections', ['title'=>'Smart Repository','activePage'=>'collections','titlePage'=>'Collections','collections'=>$collections]);
    }

    public function save(Request $request){
         if(empty($request->input('collection_id'))){
            $c = new \App\Collection;
         }
         else{
            $c = \App\Collection::find($request->input('collection_id'));
         }
         $c->name = $request->input('collection_name');
         $c->description = $request->input('description');
         $c->type = empty($request->input('collection_type'))?'Public':$request->input('collection_type');
         $c->storage_drive = $request->input('storage_drive');
         $c->content_type = $request->input('content_type');
         $c->require_approval = $request->input('require_approval');
         $c->user_id = Auth::user()->id;
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

    public function collection($collection_id){
        $collection = Collection::find($collection_id);
        $documents = \App\Document::where('collection_id','=',$collection_id)->orderby('updated_at','DESC')->paginate(100);
        return view('collection', ['collection'=>$collection, 'documents'=>$documents, 'activePage'=>'collection','titlePage'=>'Collections', 'title'=>'Smart Repository']);
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
                $user_permissions['p'.$u_p->permission_id] = 1;
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
          foreach($request->permission as $p){
            $user_permission = new \App\UserPermission;
            $user_permission->user_id = $user->id;
            $user_permission->collection_id = $request->collection_id;
            $user_permission->permission_id = $p; 
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

    public function getTitleFilteredDocuments($request, $documents){
        $title_filter = Session::get('title_filter');
		if(!empty($title_filter[$request->collection_id])){
			$documents = $documents->where('title','like','%'.$title_filter[$request->collection_id].'%');
		}
		return $documents;
	}

    public function getMetaFilteredDocuments($request, $documents){
        $all_meta_filters = Session::get('meta_filters');
        $meta_filters = empty($all_meta_filters[$request->collection_id])?null:$all_meta_filters[$request->collection_id];
        foreach($meta_filters as $mf){
            if($mf['operator'] == '='){
                $documents->whereHas('meta', function (Builder $query) use($mf){
                        $query->where('meta_field_id',$mf['field_id'])->where('value', $mf['value']);
                    }
                )->get();
            }
            else if($mf['operator'] == '>='){
                $documents->whereHas('meta', function (Builder $query) use($mf){
                        $query->where('meta_field_id',$mf['field_id'])->where('value', '>=', $mf['value']);
                    }
                )->get();
            }
            else if($mf['operator'] == '<='){
                $documents->whereHas('meta', function (Builder $query) use($mf){
                        $query->where('meta_field_id',$mf['field_id'])->where('value', '<=', $mf['value']);
                    }
                )->get();
            }
            else if($mf['operator'] == 'contains'){
                $documents->whereHas('meta', function (Builder $query) use($mf){
                        $query->where('meta_field_id',$mf['field_id'])->where('value', 'like', '%'.$mf['value'].'%');
                    }
                )->get();
            }
        }
        return $documents;
    }

    // wrapper function for search
    public function search(Request $request){
        if(!empty(env('SEARCH_MODE')) && env('SEARCH_MODE') == 'elastic'){
            return $this->searchElastic($request);
        }
        else{
            return $this->searchDB($request); 
        }
    }

    // elastic search
    public function searchElastic($request){
        $elastic_hosts = env('ELASTIC_SEARCH_HOSTS', 'localhost:9200');
        $hosts = explode(",",$elastic_hosts);
        $client = ClientBuilder::create()->setHosts($hosts)->build();
    
        $params = array();
        /*
        $params = [
            'index' => 'sr_documents',
            'body'  => [
                'query' => [
                    'bool'=>[
                        'filter' => [
                            'term'=> ['collection_id' => $request->collection_id]
                        ]
                    ]
                ]
            ]
        ];
        */

	if(!empty($request->collection_id)){
		$collection = \App\Collection::find($request->collection_id);
		if($collection->content_type == 'Uploaded documents'){
        	$elastic_index = 'sr_documents';
        	$documents = \App\Document::where('collection_id', $request->collection_id);
		}
		else{
        	$elastic_index = 'sr_urls';
        	$documents = \App\Url::where('collection_id', $request->collection_id);
		}
	}
	else {
		// search all documents
		$collections = $this->userCollections();
		$collection_ids = array();
		foreach($collections as $c){
			$collection_ids[] = $c->id;
		}
		$collection_type = $request->collection_type;
		if($collection_type == 'Web resources'){
        		$documents = \App\Url::whereIn('collection_id', $collection_ids);
        		$elastic_index = 'sr_urls';
		}
		else{
        		$documents = \App\Document::whereIn('collection_id', $collection_ids);
        		$elastic_index = 'sr_documents';
		}
	}
        $total_count = $documents->count();

        if(!empty($request->search['value']) && strlen($request->search['value'])>3){
            $search_term = $request->search['value'];
            $words = explode(' ',$search_term);
            /*
            $params['body']['query']['simple_query_string']['fields'] = ['text_content','title'];
            $params['body']['query']['simple_query_string']['query'] = $search_term;
            */
            foreach($words as $w){
                $params['body']['query']['bool']['must'][]['wildcard']['text_content']=$w.'*';
            }
	    if(!empty($request->collection_id)){
            	$params['body']['query']['bool']['filter']['term']['collection_id']=$request->collection_id;
	    }
        }
        $columns = array('type', 'title', 'size', 'updated_at');
        if(!empty($params)){
	    $params['index'] = $elastic_index;
	    $params['size'] = 100;// set a max size returned by ES
            $response = $client->search($params);
            $document_ids = array();
            foreach($response['hits']['hits'] as $h){
                $document_ids[] = $h['_id'];
            }
            $documents = $documents->whereIn('id', $document_ids);
        }
        // get title filtered documents
		if(!empty(Session::get('title_filter'))){
            $documents = $this->getTitleFilteredDocuments($request, $documents);
		}
        // get Meta filtered documents
        $all_meta_filters = Session::get('meta_filters');
        if(!empty($all_meta_filters[$request->collection_id])){
            $documents = $this->getMetaFilteredDocuments($request, $documents);
        }

	// get approval exception 
	// the exceptions will be removed from the models with ->whereNotIn 
	//$approval_exceptions = $this->getApprovalExceptions($request, $documents);
	$documents = $this->approvalFilter($request, $documents);
	//$documents = $documents->get();
	$filtered_count = $documents->count(); 

	$sort_column = @empty($columns[$request->order[0]['column']])?'updated_at':$columns[$request->order[0]['column']];
	$sort_direction = @empty($request->order[0]['dir'])?'desc':$request->order[0]['dir'];
	$length = empty($request->length)?10:$request->length;
	$documents = $documents
		->orderby($sort_column,$sort_direction)
             ->limit($length)->offset($request->start)->get();

	$has_approval = \App\Collection::where('id','=',$request->collection_id)
		->where('require_approval','=','1')->get();

		if($request->is('api/*')){
			return 
        	 array(
            'data'=>$documents,
            'draw'=>(int) $request->draw,
            'recordsTotal'=> $total_count,
            'recordsFiltered' => $filtered_count,
            'error'=> '',
        	);
		}
		else{
        	$results_data = $this->datatableFormatResults(
               array('request'=>$request, 'documents'=>$documents, 'has_approval'=>$has_approval)
       		);
		}
        $results= array(
            'data'=>$results_data,
            'draw'=>(int) $request->draw,
            'recordsTotal'=> $total_count,
            'recordsFiltered' => $filtered_count,
            'error'=> '',
        );
		// logging of search is not done here
		// refer to the searchDB function below
        return json_encode($results, JSON_UNESCAPED_UNICODE);
    }

    // db search (default)
    public function searchDB($request){
	if(!empty($request->collection_id)){
		$collection = \App\Collection::find($request->collection_id);
		if($collection->content_type == 'Uploaded documents'){
        	$documents = \App\Document::where('collection_id', $request->collection_id);
		}
		else{
        	$documents = \App\Url::where('collection_id', $request->collection_id);
		}
	}
	else {
		// search all documents
		$collections = $this->userCollections();
		$collection_ids = array();
		foreach($collections as $c){
			$collection_ids[] = $c->id;
		}
		$collection_type = $request->collection_type;
		if($collection_type == 'Web resources'){
        		$documents = \App\Url::whereIn('collection_id', $collection_ids);
        		$elastic_index = 'sr_urls';
		}
		else{
        		$documents = \App\Document::whereIn('collection_id', $collection_ids);
        		$elastic_index = 'sr_documents';
		}
	}
        $columns = array('type', 'title', 'size', 'updated_at');
	$has_approval = \App\Collection::where('id','=',$request->collection_id)->where('require_approval','=','1')->get();
	//
        // total number of viewable records
        $total_documents = $documents->count(); 

        // get title filtered documents
		if(!empty(Session::get('title_filter'))){
            $documents = $this->getTitleFilteredDocuments($request, $documents);
		}
        // get Meta filtered documents
        $all_meta_filters = Session::get('meta_filters');
        if(!empty($all_meta_filters[$request->collection_id])){
            $documents = $this->getMetaFilteredDocuments($request, $documents);
        }

        // content search
        if(!empty($request->search['value']) && strlen($request->search['value'])>3){
            $documents = $documents->search($request->search['value']);
        }
	// get approval exception 
	// the exceptions will be removed from the models with ->whereNotIn 
	//$approval_exceptions = $this->getApprovalExceptions($request, $documents);
	$documents = $this->approvalFilter($request, $documents);
	$filtered_count = $documents->count(); //- count($approval_exceptions);

    if(!empty($request->embedded)){ 		
		$documents = $documents
			 ->limit($request->length)->offset($request->start)->get();
   	    $results_data = $this->datatableFormatResultsEmbedded(
			array('request'=>$request, 
			'documents'=>$documents, 
			'has_approval'=>$has_approval));
	}
	else{
		$sort_column = @empty($columns[$request->order[0]['column']])?'updated_at':$columns[$request->order[0]['column']];
		$sort_direction = @empty($request->order[0]['dir'])?'desc':$request->order[0]['dir'];
		$length = empty($request->length)?10:$request->length;
		$documents = $documents
			->orderby($sort_column,$sort_direction)
            ->limit($length)->offset($request->start)->get();
		if($request->is('api/*')){
			return 
        	 array(
            'data'=>$documents,
            'draw'=>(int) $request->draw,
            'recordsTotal'=> \App\Document::where('collection_id',$request->collection_id)->count(),
            'recordsFiltered' => $filtered_count,
            'error'=> '',
        	);
		}
		else{
        	$results_data = $this->datatableFormatResults(
				array('request'=>$request, 
				'documents'=>$documents, 
				'has_approval'=>$has_approval));
		}
	}
        
        $results= array(
            'data'=>$results_data,
            'draw'=>(int) $request->draw,
            'recordsTotal'=> $total_documents,
            'recordsFiltered' => $filtered_count,
            'error'=> '',
        );

        // log search query
        $search_log_data = array('collection_id'=> $request->collection_id, 
                'user_id'=> empty(\Auth::user()->id) ? null : \Auth::user()->id,
                'search_query'=> $request->search['value'], 
                'meta_query'=>'',
                'results'=>$filtered_count);
        if(!empty($request->search['value']) && strlen($request->search['value'])>3){
	    	if(!empty($request->collection_id)){
            	$this->logSearchQuery($search_log_data);
	    	}
        }
        return json_encode($results, JSON_UNESCAPED_UNICODE);
    }

    private function approvalFilter($request, $documents){
		if(empty($request->collection_id)){ // this is a search within all documents
			// approved where approval is needed
			$documents = $documents
			->where(function($query){
				$query->where(function ($Q){
					$Q->whereNotNull('approved_on')
					->whereHas('collection',function ($q){
							$q->where('require_approval',1);
					});
				});
				$query->orWhere(function($Q){
					$Q->whereHas('collection', function($q){
						$q->where('require_approval', 0)
						->orWhereNull('require_approval');
					});
				});
			});
			return $documents;
		}
		$collection = Collection::find($request->collection_id);
		if($collection->content_type == 'Web resources'){
			// all
			return $documents;
		}
		else if($collection->content_type == 'Uploaded documents'){
			if($collection->require_approval == 1){ 
				if(Auth::user()->hasPermission($collection->id, 'APPROVE')){ // return all
					return $documents;
				}
				else{ // return only approved
					$documents = $documents->whereNotNull('approved_on');	
					return $documents;
				}
			}
			else{ 
				return $documents;
			}
		}
		/*
	    $doc_col = $documents->get();
	    $filtered_docs = $doc_col->filter(function($d, $key){
		    if($d->collection->require_approval == 1 && 
			    !Auth::user()->hasPermission($d->collection->id, 'APPROVE') &&
		    	    empty($d->approved_on)){
			    return true;
		    }
	    });
	    $filtered_ids = array();
	    foreach($filtered_docs as $d){
		    $filtered_ids[] = $d->id;
	    }
	    return $filtered_ids;
		*/
    }

    private function datatableFormatResults($data){
        $documents = $data['documents'];
        $request = $data['request'];
        $has_approval = $data['has_approval'];

		if(!empty($request->collection_id)){
			$collection = \App\Collection::find($request->collection_id);
			$content_type = $collection->content_type;
			$column_config = json_decode($collection->column_config);	
		}
		else{
			// default content type 
			$content_type = 'Uploaded documents';
		}

        $results_data = array();
        foreach($documents as $d){
            $action_icons = '';

	    if($content_type == 'Uploaded documents'){
            	$revisions = $d->revisions;
            	$r_count = count($revisions);
            	if($r_count > 1){
               		$filter_count = ($r_count > 9) ? '' : '_'.$r_count;
                	$action_icons .= '<a class="btn btn-primary btn-link" href="/document/'.$d->id.'/revisions" title="'.$r_count.' revisions"><i class="material-icons">filter'.$filter_count.'</i></a>';
            	}
		$action_icons .= '<a class="btn btn-primary btn-link" title="Download" href="/collection/'.$d->collection_id.'/document/'.$d->id.'" target="_blank"><i class="material-icons">cloud_download</i></a>';
	    }
  	    else if ($content_type == 'Web resources'){		
		$action_icons .= '<a class="btn btn-primary btn-link" href="'.$d->url.'" target="_blank"><i class="material-icons">link</i></a>';
	    }

		if(env('ENABLE_INFO_PAGE') == 1){
	    $action_icons .= '<a class="btn btn-primary btn-link" title="Information and more" href="/collection/'.$d->collection_id.'/document/'.$d->id.'/details"><i class="material-icons">info</i></a>';
		}
	    if($content_type == 'Uploaded documents'){
            if(Auth::user()){
                if(Auth::user()->canApproveDocument($d->id) && !$has_approval->isEmpty()){
			if(!empty($d->approved_on)){
                $action_icons .= '<a class="btn btn-primary btn-link" href="/document/'.$d->id.'/edit" title="UnApprove document"><i class="material-icons">done</i></a>';
			}
			else{
                $action_icons .= '<a class="btn btn-primary btn-link" href="/document/'.$d->id.'/edit" title="Approve document"><i class="material-icons">close</i></a>';
			}
		}
                if(Auth::user()->canEditDocument($d->id)){
                $action_icons .= '<a class="btn btn-success btn-link" href="/document/'.$d->id.'/edit" title="Create a new revision"><i class="material-icons">edit</i></a>';
                }
                if(Auth::user()->canDeleteDocument($d->id)){
                $action_icons .= '<span class="btn btn-danger btn-link confirmdelete" onclick="showDeleteDialog('.$d->id.');" title="Delete document"><i class="material-icons">delete</i></span>';
                }
            }
	    } // if collection's content-type == Uploaded documents
	    //$title = $d->title.': '. substr($d->text_content, 0, 100).' ...';
	    $title = $d->title;
	    $title = mb_convert_encoding($title, 'UTF-8', 'UTF-8');
	    //$title = $d->title;
        $result = array(
                //'type' => array('display'=>'<a href="/collection/'.$d->collection_id.'/document/'.$d->id.'/details"><img class="file-icon" src="/i/file-types/'.$d->icon().'.png" /></a>', 'filetype'=>$d->icon()),
                'type' => array('display'=>'<img class="file-icon" src="/i/file-types/'.$d->icon().'.png" />', 'filetype'=>$d->icon()),
                'title' => $title,
                'size' => array('display'=>$d->human_filesize(), 'bytes'=>$d->size),
                'updated_at' => array('display'=>date('d-m-Y', strtotime($d->updated_at)), 'updated_date'=>$d->updated_at),
                'actions' => $action_icons
			);
		if(!empty($collection)){
			foreach($collection->meta_fields as $m){
			$result['meta_'.$m->id] = $d->meta_value($m->id);
			}
		}

		$results_data[] = $result;
		} // foreach ends
        return $results_data;
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

    public function replaceMetaFilter(Request $request){
        // set filters in session and return to the collection view 
        $meta_filters = Session::get('meta_filters');
		$new_meta_filters = array();
        if(!empty($request->meta_value)){
			if($meta_filters && is_array($meta_filters[$request->collection_id])){
			foreach($meta_filters[$request->collection_id] as $m){
				if($m['field_id'] != $request->meta_field){
					$new_meta_filters[$request->collection_id][] = $m;
				}
			}
			}
            $new_meta_filters[$request->collection_id][] = array(
                'filter_id'=>\Uuid::generate()->string,
                'field_id'=>$request->meta_field,
                'operator'=>$request->operator,
                'value'=>$request->meta_value
            );
        }
        Session::put('meta_filters', $new_meta_filters);
        return redirect('/collection/'.$request->collection_id);
    }

	public function replaceTitleFilter(Request $request){
		$title_filter = Session::get('title_filter');
		$title_filter[$request->collection_id] = $request->title_filter;
		Session::put('title_filter', $title_filter);
        return redirect('/collection/'.$request->collection_id);
	}
    
    public function metaInformation($collection_id, $meta_field_id=null){
        $collection = \App\Collection::find($collection_id);
        if(empty($meta_field_id)){
            $edit_field = new \App\MetaField;
        }
        else{
            $edit_field = \App\MetaField::find($meta_field_id);
        }
        $meta_fields = $collection->meta_fields()->orderby('display_order','ASC')->get();
        return view('metainformation', ['collection'=>$collection, 
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
        $meta_field->type = $request->input('type');
        $meta_field->options = $request->input('options');
        $meta_field->display_order = $request->input('display_order');
        $meta_field->save();
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
        $all_meta_filters = Session::get('meta_filters');
        $all_meta_filters[$collection_id] = null;
        Session::put('meta_filters', $all_meta_filters);
        return redirect('/collection/'.$collection_id);
	}

    public function logSearchQuery($data){
        $search_log_entry = new \App\Searches;
        $search_log_entry->collection_id = $data['collection_id']; 
        $search_log_entry->meta_query = $data['meta_query']; 
        $search_log_entry->search_query = $data['search_query']; 
        $search_log_entry->user_id = $data['user_id']; 
        $search_log_entry->results = $data['results']; 
        $search_log_entry->save();
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

    public function collection_list(){
        /*
	 !! LOOKS LIKE A DUPLICATE FUNCTION of list() !!
         Get all public collections 
         plus collections to which the current user has access.
         Access to members-only collection is determined by db_table:user_permissions 
        */
        $user_collections = array();
        $user_permissions = empty(Auth::user()) ? array() : Auth::user()->accessPermissions();
        foreach($user_permissions as $u_p){
            if(!in_array($u_p->collection_id, $user_collections)){
                array_push($user_collections, $u_p->collection_id);
            }
        }
        $collections = Collection::whereIn('id', $user_collections)->orWhere('type','=','Public')->get();
	return $collections;
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
        }
        else{
            $collection = \App\Collection::find($collection_id);
        }
        return view('save_exclude_sites', ['collection'=>$collection,'activePage'=>'Collection', 'titlePage'=>'Collection']);
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
	    	$sd->save();
		$last_insert_id = $sd->id;
		if(!empty($request->input('save_urls'))){
		$this->saveDesiredUrls($last_insert_id,$request);		
		}
		elseif(!empty($request->input('exclude_urls'))){
		$this->excludeUrls($last_insert_id,$request);		
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

    public function saveDesiredUrls($spidered_domain_id, $request){
	$url_start_patterns = explode("\n",$request->input('save_urls'));
	$collection_id = $request->input('collection_id');
#DB::enableQueryLog();
	foreach($url_start_patterns as $url){
		$su = new \App\DesiredUrl;	
		$su->collection_id = $collection_id;
		$su->url_start_pattern = rtrim(ltrim($url));
		$su->spidered_domain_id = $spidered_domain_id;
	    	$su->save();
	}
#dd(DB::getQueryLog());
    }	

    public function excludeUrls($spidered_domain_id, $request){
	$url_start_patterns = explode("\n",$request->input('save_urls'));
	$collection_id = $request->input('collection_id');
	foreach($url_start_patterns as $url){
		$su = new \App\UrlSuppression;
		$su->collection_id = $collection_id;
		$su->url_start_pattern = rtrim(ltrim($url));
		$su->spidered_domain_id = $spidered_domain_id;
	    	$su->save();
	}
    }	

	// column-config
	public function showSettingsForm(Request $request){
		$collection = Collection::find($request->collection_id);
        return view('collection-settings', ['collection'=>$collection, 
			'mailbox'=>CollectionMailbox::where('collection_id', $collection->id)->first()]);
	}
	
	public function saveSettings(Request $request){
		$collection = Collection::find($request->collection_id);
		$col_config = array(
			'title' => $request->input('title'),
			'title_search' => $request->input('title_search'),
			'type'=>$request->input('type'),
			'creation_time'=>$request->input('creation_time'),
			'size'=>$request->input('size'),
			'meta_fields'=>$request->input('meta_fields'),
			'meta_fields_search'=>$request->input('meta_fields_search')
		);
		$collection->column_config = json_encode($col_config);
		$collection->save();
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

	public function export($collection_id){
		$documents = \App\Document::where('collection_id', $collection_id)->get();
		$collection = \App\Collection::find($collection_id);
		$meta_fields = $collection->meta_fields;
		$filename = $collection->name;
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename={$filename}.tsv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo 'id'."\t".'title'."\t";
		foreach($meta_fields as $m){
			echo $m->label."\t";
		}
		echo "\n";
		foreach($documents as $d){
			// remove tab spaces from the title, if present
			echo $d->id."\t".preg_replace("/\t/", " ", $d->title)."\t";
			foreach($meta_fields as $m){
				if(!empty($d->meta_value($m->id))){
					echo preg_replace("/\t/"," ", $d->meta_value($m->id));
				}
				else{
					echo " - ";
				}
				echo "\t";
			}
			echo "\n";
		}
		exit;
	}

}
