<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Collection;


class CountryController extends Controller
{
     public function index()
    {
    $collections = Collection::where('parent_id', null)->get();
	return view('isa.countries',['collections'=>$collections, 'activePage'=>'Collections','titlePage'=>'Collections']);
    }

}