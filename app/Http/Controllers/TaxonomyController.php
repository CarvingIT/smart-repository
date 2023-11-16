<?php

namespace App\Http\Controllers;

use App\Taxonomy;
use App\Http\Requests\TaxonomyRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Session;

class TaxonomyController extends Controller
{
     public function index(Taxonomy $taxonomy)
    {
  
	return view('taxonomiesmanagement', ['taxonomies'=>$taxonomy->all(), 'activePage'=>'taxonomies-management', 'titlePage' => 'Taxonomies']);
    }

    public function create()
    {
	$parent_taxonomies = Taxonomy::all();
        return view('taxonomies.form',['parent_taxonomies'=>$parent_taxonomies]);
    }

    public function store(TaxonomyRequest $request, Taxonomy  $taxonomy)
    {
	$taxonomy->create($request->post());
	Session::flash('alert-success', 'Taxonomy successfully created.');
	return redirect()->route('taxonomies.index')->withStatus(__('Taxonomy successfully created.'));
    }
    
    public function add($id)
    {
    $taxonomy  = Taxonomy::find($id);
	$parent_taxonomies = Taxonomy::all();
    return view('taxonomies.add',['taxonomy'=>$taxonomy,'parent_taxonomies'=>$parent_taxonomies]);
    }

    public function addstore(TaxonomyRequest $request, $id)
    {
	$taxonomy_new = new Taxonomy();
    $taxonomy_new ->label = $request->label;
    $taxonomy_new->parent_id = $id;
	$taxonomy_new ->save();

	Session::flash('alert-success', 'Taxonomy successfully added.');
	return redirect()->route('taxonomies.index');
    }

    public function edit($id)
    {
    $taxonomy  = Taxonomy::find($id);
	$parent_taxonomies = Taxonomy::all();
    return view('taxonomies.edit', ['taxonomy'=>$taxonomy,'parent_taxonomies'=>$parent_taxonomies]);
    }

    public function update(TaxonomyRequest $request, $id)
    {
	$taxonomies = Taxonomy::find($id);
    $taxonomies ->parent_id = $request->parent_id;
	$taxonomies ->label = $request->label;
	$taxonomies ->save();

	Session::flash('alert-success', 'Taxonomy successfully updated.');
        return redirect()->route('taxonomies.index')->withStatus(__('Taxonomy successfully updated.'));
    }

    public function destroy(Request $request)
    {
	$taxonomy = \App\Taxonomy::findOrFail($request->taxonomy_id);
	if(!empty($request->delete_captcha) &&
                $request->delete_captcha == $request->delete_captcha){
        	$taxonomy->delete();
		Session::flash('alert-success', 'Taxonomy successfully deleted.');
        	return redirect()->route('taxonomies.index');
        }
	else{
		Session::flash('alert-danger', 'Please fill Captcha');
        	return redirect('/taxonomies');
        }
    }
}
