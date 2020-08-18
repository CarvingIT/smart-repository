<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Collection;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

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
        return view('collection-form', ['collection'=>$collection,'activePage'=>'Collection', 'titlePage'=>'Collection']);
    }

    public function list(){
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
         $c->description = $request->input('description');
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
	$request->validate([
    	'user_id' => 'email:rfc,dns'
	]);
        $user = \App\User::where('email','=',$request->user_id)->first();
        // first delete all permissions on the collection
        \App\UserPermission::where('collection_id','=',$request->collection_id)
            ->where('user_id','=',$user->id)->delete(); 
        foreach($request->permission as $p){
            $user_permission = new \App\UserPermission;
            $user_permission->user_id = $user->id;
            $user_permission->collection_id = $request->collection_id;
            $user_permission->permission_id = $p; 
            $user_permission->save();
        }
        return $this->collectionUsers($request->collection_id);
    }

    public function removeUser($collection_id, $user_id){
        \App\UserPermission::where('collection_id','=',$collection_id)
            ->where('user_id','=',$user_id)->delete(); 
        return $this->collectionUsers($collection_id);
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

    public function search(Request $request){
	$has_approval = \App\Collection::where('id','=',$request->collection_id)->where('require_approval','=','1')->get();
        $columns = array('type', 'title', 'size', 'updated_at');
    if(Auth::user()){ // and has permission APPROVE on this collection!!!
        	$documents_filtered = \App\Document::where('collection_id','=',$request->collection_id);
	}
	else{
		if($has_approval->isEmpty()){
        	$documents_filtered = \App\Document::where('collection_id','=',$request->collection_id);
		}
		else{
        	$documents_filtered = \App\Document::where('collection_id','=',$request->collection_id)->whereNotNull('approved_on');
		}
	}
        $total_documents = $documents_filtered->count(); // total number of viewable records
        // get Meta filtered documents
        $all_meta_filters = Session::get('meta_filters');
        if(!empty($all_meta_filters[$request->collection_id])){
            $documents_filtered = $this->getMetaFilteredDocuments($request, $documents_filtered);
        }
        // and take intersection with $documents filtered
        // to get a new set of "documents_filtered

        if(!empty($request->search['value']) && strlen($request->search['value'])>3){
            $documents_filtered = $documents_filtered->search($request->search['value']);
        }
            $filtered_count = $documents_filtered->count();
            $documents = $documents_filtered->orderby($columns[$request->order[0]['column']],$request->order[0]['dir'])
            ->limit($request->length)->offset($request->start)->get();
        
        $results_data = array();
	##$has_approval = \App\Collection::where('id','=',$request->collection_id)->where('require_approval','=','1')->get();
        foreach($documents as $d){
            $action_icons = '';
                $action_icons .= '<a class="btn btn-primary btn-link" href="/document/'.$d->id.'/revisions" title="View revisions"><i class="material-icons">view_column</i>
                                <div class="ripple-container"></div></a>';
            if(Auth::user()){
                if(Auth::user()->canApproveDocument($d->id) && !$has_approval->isEmpty()){
			if(!empty($d->approved_on)){
                $action_icons .= '<a class="btn btn-primary btn-link" href="/document/'.$d->id.'/edit" title="UnApprove document"><i class="material-icons">done</i>
                                <div class="ripple-container"></div></a>';
			}
			else{
                $action_icons .= '<a class="btn btn-primary btn-link" href="/document/'.$d->id.'/edit" title="Approve document"><i class="material-icons">close</i>
                                <div class="ripple-container"></div></a>';
			}
		}
                if(Auth::user()->canEditDocument($d->id)){
                $action_icons .= '<a class="btn btn-success btn-link" href="/document/'.$d->id.'/edit" title="Create a new revision"><i class="material-icons">edit</i>
                                <div class="ripple-container"></div></a>';
                }
                if(Auth::user()->canDeleteDocument($d->id)){
                $action_icons .= '<a class="btn btn-danger btn-link" href="/document/'.$d->id.'/delete" title="Delete document"><i class="material-icons">delete</i>
                                <div class="ripple-container"></div></a>';
                }
            }
            $results_data[] = array('type' => '<img class="file-icon" src="/i/file-types/'.$d->icon().'.png" />',
                        'title' => '<a href="/document/'.$d->id.'" target="_new">'.$d->title.'</a>',
                        //'size' => $d->human_filesize(),
                        'size' => array('display'=>$d->human_filesize(), 'bytes'=>$d->size),
                        'updated_at' => array('display'=>date('F d, Y', strtotime($d->updated_at)), 'updated_date'=>$d->updated_at),
                        'actions' => $action_icons);
        }

        $results = array(
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
            $this->logSearchQuery($search_log_data);
        }
        return json_encode($results);
    }

    public function setMetaFilters(Request $request){
        // set filters in session and return to the collection view 
        $params = $request->all(); 
        $meta_filters = array();
        foreach($params as $k=>$v){
            if(preg_match('/^meta_field_/',$k) && !empty($v)){
                $field_id = str_replace('meta_field_','', $k);
                $operator = $params['operator_'.$field_id];
                $meta_filters[$request->collection_id][] = array('field_id'=>$field_id, 
                    'operator'=>$operator, 
                    'value'=>$params['meta_field_'.$field_id]);
            }
        }
        Session::put('meta_filters', $meta_filters);
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
        $meta_fields = \App\MetaField::where('collection_id', '=', $collection_id)->orderby('display_order','ASC')->get();
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

    public function deleteMetaField($meta_field_id){
        $meta_field = \App\MetaField::find($meta_field_id);
        $collection_id = $meta_field->collection_id;
        $meta_field->delete();
        return $this->metaInformation($collection_id);
    }

    public function metaFiltersForm($collection_id){
        $collection = \App\Collection::find($collection_id);
        return view('metasearch', ['collection'=>$collection, 
            'activePage'=>'Set Meta Filters',
            'titlePage'=>'Set Meta Filters',
            'title'=>'Set Meta Filters']);
    }
    
    public function removeMetaFilter($collection_id, $field_id){
        $all_meta_filters = Session::get('meta_filters');
        $new_collection_filters = array();
        foreach($all_meta_filters[$collection_id] as $mf){
            if($mf['field_id'] == $field_id) continue;
            $new_collection_filters[] = $mf;
        }
        $all_meta_filters[$collection_id] = $new_collection_filters;
        Session::put('meta_filters', $all_meta_filters);
        return redirect('/collection/'.$collection_id);
    }

    public function removeAllMetaFilters($collection_id){
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

    public function deleteCollection($collection_id){
        $collection = \App\Collection::find($collection_id);

    	if ($collection != null) {
       	 	if($collection->delete())
            	Session::flash('alert-success', 'Collection deleted successfully!');
       	 	return redirect('/admin/collectionmanagement');
    	}

    }

    public function collection_list(){
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

## Class Ends
}
