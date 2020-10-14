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
        $this->middleware(['auth'=>'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        #return view('home',['title'=>'Home','activePage'=>'Home','titlePage' => 'Home']);
	$collections = Collection::all();
	$my_collection = new CollectionController;
	$my_collection_list = $my_collection->collection_list();
        return view('dashboard',['my_collections' => $my_collection_list, 'collections' => $collections, 'title'=>'Home','activePage'=>'Home','titlePage' => 'Home']);
    }
}
