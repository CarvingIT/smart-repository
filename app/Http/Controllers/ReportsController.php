<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Collections;

class ReportsController extends Controller
{

    public function index(){
        return view('reports-index',['titlePage'=>'Reports','activePage'=>'Reports']);
    }

    public function downloads(Request $request){
       /* $hits = \DB::table('document_downloads')
            ->select(\DB::raw('DATE(added_on) as date'), \DB::raw('count(id) as cnt'))
            ->groupBy('date')->get();
	*/
	if(empty($request->collection_id)){
        $hits = \DB::table('document_downloads')
            ->select(\DB::raw('DATE(added_on) as date'), \DB::raw('count(document_downloads.id) as cnt'));
        }
        else{
        $hits = \DB::table('document_downloads')
            ->select(\DB::raw('DATE(added_on) as date'), \DB::raw('count(document_downloads.id) as cnt'))
            ->join('documents', 'documents.id','=','document_id')
            ->join('collections', 'documents.collection_id','=','collections.id')
            ->where('collections.id','=', $request->collection_id);
            //print_r($hits->toSql());
            //exit;
        }

        $hits = $hits->groupBy('date')->get();

        $collection_list = \App\Collection::all();
        $list = array();
        foreach($collection_list as $collection){
                $list[] = array('id'=>$collection->id,'name'=>$collection->name);
        }

        return view('report-date-count',['hits'=>$hits, 'name'=>'Downloads','titlePage'=>'Downloads','activePage'=>'downloads', 'collection_list'=>$list,'collection_id'=>$request->collection_id]);
    }

    public function uploads(Request $request){
	if(empty($request->collection_id)){ 
        $hits = \DB::table('document_revisions')
            ->select(\DB::raw('DATE(document_revisions.created_at) as date'), \DB::raw('count(document_revisions.id) as cnt'));
	}
	else{
        $hits = \DB::table('document_revisions')
            ->select(\DB::raw('DATE(document_revisions.created_at) as date'), \DB::raw('count(document_revisions.id) as cnt'))
	    ->join('documents', 'documents.id','=','document_id')
	    ->join('collections', 'documents.collection_id','=','collections.id')
	    ->where('collections.id','=', $request->collection_id);
	    //print_r($hits->toSql());
	    //exit;
	}

        $hits = $hits->groupBy('date')->get();

	$collection_list = \App\Collection::all();
	$list = array();
	foreach($collection_list as $collection){
		$list[] = array('id'=>$collection->id,'name'=>$collection->name);
	}

        return view('report-date-count',['hits'=>$hits, 'name'=>'Uploads', 'titlePage'=>'Uploads','activePage'=>'uploads','collection_list'=>$list,'collection_id'=>$request->collection_id]);
    }


}
