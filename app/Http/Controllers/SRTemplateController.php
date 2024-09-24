<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SRTemplate;
use Session;
use App\Collection;

class SRTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $templates = \App\SRTemplate::all();
         return view('srtemplatemanagement',["templates"=>$templates]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
	if(!auth()->user()->hasRole('admin')){
                 return abort(403);
        }
        return view('templates.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		if(empty($template_id)){
			$t = new SRTemplate();
		}
		else{
			$t = SRTemplate::find($request->template_id);
		}
		$t->template_name = $request->template_name;
		$t->html_code = $request->html_code;
		$t->collection_id = $request->collection_id;
		$t->description = $request->description;
		$t->save();
		return redirect('/admin/srtemplatemanagement');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
	$template = \App\SRTemplate::find($id);
	return view('templates.edit',['template'=>$template]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
	        $t = SRTemplate::find($id);
                $t->template_name = $request->template_name;
                $t->html_code = $request->html_code;
                $t->collection_id = $request->collection_id;
                $t->description = $request->description;
                $t->save();
                return redirect('/admin/srtemplatemanagement');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
	$template = SRTemplate::find($request->template_id);
	if(!empty($request->delete_captcha) && ($request->delete_captcha == $request->hidden_captcha)){
		$template->delete();
		Session::flash('alert-success', 'Template successfully deleted.');
                return redirect()->route('template.index');
        }
        else{
                Session::flash('alert-danger', 'Please fill Captcha');
                return redirect('/admin/srtemplatemanagement');
        }
    }
}
