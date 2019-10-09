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
        //$documents = $collection->documents()->paginate(10);
        $documents = \App\Document::where('collection_id','=',$collection_id)->orderby('id','DESC')->paginate(10);
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
}
