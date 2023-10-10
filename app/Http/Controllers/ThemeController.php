<?php

namespace App\Http\Controllers;

use App\Taxonomy;


class ThemeController extends Controller
{
     public function index(Taxonomy $taxonomy)
    {
  
	return view('isa.themes', ['taxonomies'=>$taxonomy->all(), 'activePage'=>'taxonomies-management', 'titlePage' => 'Taxonomies']);
    }
}
?>