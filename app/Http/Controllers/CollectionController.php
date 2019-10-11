<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Collection;
use Illuminate\Support\Facades\Auth;
use Session;

class CollectionController extends Controller
{
    public function __construct()
    {
        //$this->middleware('collection_view');
    }

    public function index(){
        $collections = Collection::all();
        return view('collectionmanagement', ['collections'=>$collections]);
    }

    public function add_edit_collection($collection_id){
        if($collection_id == 'new'){
            $collection = new \App\Collection();
        }
        else{
            $collection = \App\Collection::find($collection_id);
        }
        return view('collection-form', ['collection'=>$collection]);
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
        return view('collections', ['collections'=>$collections]);
    }

    public function save(Request $request){
         $id = empty($request->input('collection_id'))?'':$request->input('collection_id');
         $c = empty($id)? new Collection():Collection::find($id);
         $c->name = $request->input('collection_name');
         $c->description = $request->input('description');
         $c->type = empty($request->input('collection_type'))?'Public':$request->input('collection_type');
         $c->description = $request->input('description');
         $c->user_id = Auth::user()->id;
         try{
            $c->save();
            Session::flash('alert-success', 'Collection saved successfully!');
         }
         catch(\Exception $e){
            Session::flash('alert-danger', $e->getMessage());
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
         return redirect('/admin/collectionmanagement');
    }

    public function collection($collection_id){
        $collection = Collection::find($collection_id);
        $documents = \App\Document::where('collection_id','=',$collection_id)->orderby('updated_at','DESC')->paginate(100);
        return view('collection', ['collection'=>$collection, 'documents'=>$documents]);
    }

    public function collectionUsers($collection_id){
        $collection = Collection::find($collection_id);
        $user_permissions = \App\UserPermission::where('collection_id', '=', $collection_id)->get();
        //$user_permissions;
        $collection_users = array();
        foreach($user_permissions as $u_p){
            $collection_users[$u_p->user_id][] = $u_p;
        }
        return view('collection_users', ['collection'=>$collection, 'collection_users'=>$collection_users]);
    }

    public function showCollectionUserForm($collection_id, $user_id=null){
        $user_permissions = array();
        $user = null;
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
            'user_permissions'=>$user_permissions]);
    }

    public function saveUser(Request $request){
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

    public function search(Request $request){
        $columns = array('type', 'title', 'size', 'updated_at');
        $documents_filtered = \App\Document::where('collection_id','=',$request->collection_id);
        $total_documents = $documents_filtered->count();

        if(!empty($request->search['value']) && strlen($request->search['value'])>3){
            $documents_filtered = $documents_filtered->search($request->search['value']);
        }
            $documents = $documents_filtered->orderby($columns[$request->order[0]['column']],$request->order[0]['dir'])
            ->limit($request->length)->offset($request->start)->get();
        
        $results_data = array();
        foreach($documents as $d){
            $action_icons = '';
            if(Auth::user()){
                if(Auth::user()->canEditDocument($d->id)){
                $action_icons .= '<a href="/document/'.$d->id.'/revisions" title="View revisions"><img class="icon" src="/i/revisions.png" /></a>';
                $action_icons .= '<a href="/document/'.$d->id.'/edit" title="Create a new revision"><img class="icon" src="/i/pencil-edit-button.png" /></a>';
                }
                if(Auth::user()->canDeleteDocument($d->id)){
                $action_icons .= '<a href="/document/'.$d->id.'/delete" title="Delete document"><img class="icon" src="/i/trash.png" /></a>';
                }
            }
            $results_data[] = array('type' => '<img class="file-icon" src="/i/file-types/'.$d->icon().'.png" />',
                        'title' => '<a href="/document/'.$d->id.'" target="_new">'.$d->title.'</a>',
                        'size' => $d->size,
                        'updated_at' => date('F d, Y', strtotime($d->updated_at)),
                        'actions' => $action_icons);
        }

        $results = array(
            'data'=>$results_data,
            'draw'=>(int) $request->draw,
            'recordsTotal'=> $total_documents,
            'recordsFiltered' => $documents_filtered->count(),
            'error'=> '',
        );
        return json_encode($results);
    }
}
