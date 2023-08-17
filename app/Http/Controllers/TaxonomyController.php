<?php

namespace App\Http\Controllers;
use App\Taxonomy;
use App\Http\Requests\TaxonomyRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
//use Illuminate\Http\Request;
use Session;



class TaxonomyController extends Controller
{
     public function index(Taxonomy $taxonomy )
    {
    //$taxonomy = Taxonomy::all();
    //return view('taxonomiesmanagement', compact('taxonomy'));
	
    $taxonomy = \App\Taxonomy::where('parent_id',0)->get();
	return view('taxonomiesmanagement', ['label'=>$taxonomy->all(), 'activePage'=>'taxonomies-management', 'titlePage' => 'Taxonomies']);
    }

    public function create()
    {
        return view('taxonomies.form');
    }

    public function store(TaxonomyRequest $request, Taxonomy  $taxonomy)
    {
	$taxonomy->create($request->post());
	Session::flash('alert-success', 'Taxonomies successfully created.');
	return redirect()->route('taxonomies.index')->withStatus(__('Taxonomies successfully created.'));
    }

    public function edit($id)
    {
 	$taxonomy = Taxonomy::find($id);
        return view('taxonomies.edit', compact('taxonomies'));
    }

    public function update(TaxonomyRequest $request, $id)
    {
	$taxonomy = Taxonomy::find($id);
	$taxonomy->lable = $request->label;
	$taxonomy->save();

	Session::flash('alert-success', 'Taxonomies successfully updated.');
        return redirect()->route('taxonomies.index')->withStatus(__('Taxonomies successfully updated.'));
    }

    public function destroy(Request $request)
    {
	$taxonomy = \App\Taxonomy::findOrFail($request->taxonomies_id);
	if(!empty($request->delete_captcha) &&
                $request->delete_captcha == $request->delete_captcha){
        	$taxonomy->delete();
		Session::flash('alert-success', 'Taxonomies successfully deleted.');
        	return redirect()->route('taxonomies.index');
        }
	else{
		Session::flash('alert-danger', 'Please fill Captcha');
        	return redirect('/taxonomies');
        }
    }
}
