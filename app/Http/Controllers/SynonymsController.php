<?php

namespace App\Http\Controllers;

use App\Synonyms;
use App\Http\Requests\SynonymsRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Session;



class SynonymsController extends Controller
{
    /**
     * Display a listing of the synonyms
     *
     * @param  \App\Synonyms  $model
     * @return \Illuminate\View\View
     */
     public function index(Synonyms $model)
    {
        #return view('synonyms.index', ['synonyms' => $model->paginate(15),'activePage'=>'Synonyms']);		## This page is as it is.

        return view('synonymsmanagement', ['synonyms'=>$model->all(), 'activePage'=>'synonyms-management', 'titlePage' => 'Synonyms']);
    }

    /**
     * Show the form for creating a new synonyms
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('synonyms.form');
    }

    /**
     * Store a newly created Synonyms in storage
     *
     * @param  \App\Http\Requests\SynonymsRequest  $request
     * @param  \App\Synonyms  $synonyms
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SynonymsRequest $request, Synonyms $synonyms)
    {
	$synonyms->create($request->post());
	Session::flash('alert-success', 'Synonyms successfully created.');
	return redirect()->route('synonyms.index')->withStatus(__('Synonyms successfully created.'));
    }

    /**
     * Show the form for editing the specified Synonyms
     *
     * @param  \App\Synonyms  $synonyms
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
 	$synonyms = Synonyms::find($id);
        return view('synonyms.edit', compact('synonyms'));
    }
    /**
     * Update the specified Synonyms in storage
     *
     * @param  \App\Http\Requests\SynonymsRequest  $request
     * @param  \App\Synonyms  $synonyms
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SynonymsRequest $request, $id)
    {
	$synonyms = Synonyms::find($id);
	$synonyms->synonyms = $request->synonyms;
	$synonyms->save();

	Session::flash('alert-success', 'Synonyms successfully updated.');
        return redirect()->route('synonyms.index')->withStatus(__('Synonym successfully updated.'));
    }

    /**
     * Remove the specified Synonyms from storage
     *
     * @param  \App\Synonyms  $synonyms
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
	$synonyms = \App\Synonyms::findOrFail($request->synonyms_id);
	if(!empty($request->delete_captcha) &&
                $request->hidden_captcha == $request->delete_captcha){
        	$synonyms->delete();
		Session::flash('alert-success', 'Synonyms successfully deleted.');
        	return redirect()->route('synonyms.index');
        }
	else{
		Session::flash('alert-danger', 'Please fill Captcha');
        	return redirect('/synonyms');
        }
    }
}
