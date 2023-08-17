<?php

namespace App\Http\Controllers;
use \App\Http\Controllers\CollectionController;

use App\Collection;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\DB;
use View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		if(env('VERIFY_EMAIL'))
        $this->middleware(['auth'=>'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
	//return redirect('/collections');
	$role_id = auth()->user()->userrole(auth()->user()->id);
	$collections=\App\Collection::where('column_config','LIKE','%'.$role_id.'%')->get();
	$documents = [];
	foreach($collections as $collection){
	$documents[] = \App\Document::where('collection_id',$collection->id)->get();
	//print_r($documents);
	//echo "<hr />";
	}
	//print_r($documents);
	//exit;
	return view('dashboard',['collections'=>$collections, 'documents'=>$documents]);
    }
}
