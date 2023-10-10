<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Collection;


class ThemeController extends Controller
{
   

    public function index(){
        $collections = Collection::where('parent_id', null)->get();
        return view('isa.themes', ['collections'=>$collections, 'activePage'=>'Collections','titlePage'=>'Collections']);
    }
}

?>