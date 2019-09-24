<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Collection;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    public function index(){
        $collections = Collection::all();
        return view('collectionmanagement', ['collections'=>$collections]);
    }

    public function list(){
        $collections = Collection::all();
        return view('collections', ['collections'=>$collections]);
    }

    public function save(Request $request){
         $id = empty($request->input('id'))?'':$request->input('id');
         $c = empty($id)? new Collection():Collection::find($id)->get();
         $c->name = $request->input('collection_name');
         $c->user_id = Auth::user()->id;
         $c->save();
         return redirect('/admin/collectionmanagement');
    }
}
