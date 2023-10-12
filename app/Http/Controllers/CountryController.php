<?php

namespace App\Http\Controllers;

use App\Taxonomy;
use App\Http\Requests\TaxonomyRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Session;

class CountryController extends Controller
{
     public function index(Taxonomy $taxonomy)
    {
  
	return view('isa.countries', ['taxonomies'=>$taxonomy->all(), 'activePage'=>'taxonomies-management', 'titlePage' => 'Taxonomies']);
    }
}
?>
