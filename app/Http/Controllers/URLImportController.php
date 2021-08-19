<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Collection;
use App\ImportLink;

class URLImportController extends Controller
{
	public function index(Request $request){
		$collection = Collection::find($request->collection_id);
		$links = ImportLink::where('collection_id', $request->collection_id)->get();
		return view('url-import', ['collection'=>$collection, 'links'=>$links]);
	}

	public function add(Request $request){
		$import_link = new ImportLink;
		$import_link->collection_id = $request->collection_id;
		$import_link->url = $request->url;
		// add metadata here
		$all_params = $request->all();
        $metadata = array();
        foreach($all_params as $p=>$v){
            if(preg_match('/^meta_field_/', $p)){
                $field_id = str_replace('meta_field_','', $p);
                array_push($metadata, array('field_id'=>$field_id, 'field_value'=>$v));
            }
		}
		$import_link->title = $request->title;
		$import_link->metadata = json_encode($metadata);
		$import_link->save();
		return redirect('/collection/'.$request->collection_id.'/url-import');
	}

	public function delete(Request $request){
		$import_link = ImportLink::find($request->link_id);
		$import_link->delete();
		return redirect('/collection/'.$request->collection_id.'/url-import');
	}
}
