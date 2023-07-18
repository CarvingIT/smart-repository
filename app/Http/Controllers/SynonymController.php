<?php

namespace App\Http\Controllers;

use App\Synonym;
use App\Http\Requests\SynonymRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Session;



class SynonymController extends Controller
{
    /**
     * Display a listing of the synonyms
     *
     * @param  \App\Synonym  $model
     * @return \Illuminate\View\View
     */
     public function index(Synonym $model)
    {
        #return view('synonyms.index', ['synonyms' => $model->paginate(15),'activePage'=>'Synonyms']);		## This page is as it is.

        return view('synonymsmanagement', ['synonyms'=>$model->all(), 'activePage'=>'synonym-management', 'titlePage' => 'Synonyms']);
    }

    /**
     * Show the form for creating a new synonym
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
		//if(!auth()->synonym()->hasRole('admin')){
		//	return abort(403);
		//}
        return view('synonyms.form');
    }

    /**
     * Store a newly created Synonym in storage
     *
     * @param  \App\Http\Requests\SynonymRequest  $request
     * @param  \App\Synonym  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SynonymRequest $request,Synonym $model)
    {
        $model->create($request->merge(['password' => Hash::make($request->get('password'))])->all());
        return redirect()->route('synonyms.index')->withStatus(__('Synonym successfully created.'));
    }

    /**
     * Show the form for editing the specified Synonym
     *
     * @param  \App\Synonym  $synonym
     * @return \Illuminate\View\View
     */
    public function edit(Synonym $synonym)
    {
		//if(!auth()->synonym()->hasRole('admin')){
		//return abort(403);
		//}
        return view('synonyms.edit', compact('synonym'));
    }

    /**
     * Update the specified Synonym in storage
     *
     * @param  \App\Http\Requests\SynonymRequest  $request
     * @param  \App\Synonym  $synonym
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SynonymRequest $request, Synonym  $synonym)
    {
        $hasPassword = $request->get('password');
        $synonym->update(
            $request->merge(['password' => Hash::make($request->get('password'))])
                ->except([$hasPassword ? '' : 'password']
        ));

        return redirect()->route('synonyms.index')->withStatus(__('Synonym successfully updated.'));
    }

    public function autoComplete(Request $request){
	$synonyms = \App\Synonym::where("email","LIKE","%{$request->input('term')}%")->get();

	$results = array();
	foreach($synonyms as $u){
		$results[] = ['value' => $u->email];
	}
	if(count($results)){
        return response()->json($results);
	}
	else{
	return ['value'=>'No Result Found'];
	}
    }



    /**
     * Remove the specified Synonym from storage
     *
     * @param  \App\Synonym  $synonym
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
	$synonym = \App\Synonym::findOrFail($request->synonym_id);
	if(!empty($request->delete_captcha) &&
                $request->delete_captcha == $request->delete_captcha){
        	$synonym->delete();
		Session::flash('alert-success', 'Synonym successfully deleted.');
        	return redirect()->route('synonyms.index');
        }
	else{
		Session::flash('alert-danger', 'Please fill Captcha');
        	return redirect('/synonym');
        }
    }
}
