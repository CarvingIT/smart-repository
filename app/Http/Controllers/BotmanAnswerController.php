<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BotmanAnswer;
use Session;

class BotmanAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('botman-answers.index', ['records'=>BotmanAnswer::all(), 'activePage'=>'botman-answers', 'titlePage' => 'Manage Answers of Chatbot']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('botman-answers.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, BotmanAnswer $record)
    {
		$record->create($request->post());
		Session::flash('alert-success', 'Record successfully created.');
		return redirect()->route('botman-answers.index')->withStatus(__('Record successfully created.'));
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
 		$record = BotmanAnswer::find($id);
        return view('botman-answers.form', compact('record'));
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
		$record = BotmanAnswer::find($id);
		$record->question = $request->question;
		$record->answer = $request->answer;
		$record->save();

		Session::flash('alert-success', 'Record successfully updated.');
        return redirect()->route('botman-answers.index')->withStatus(__('Record successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
		$record = BotmanAnswer::findOrFail($id);
		if(!empty($request->delete_captcha) &&
            $request->hidden_captcha == $request->delete_captcha){
        	$record->delete();
			Session::flash('alert-success', 'Record successfully deleted.');
        	return redirect()->route('botman-answers.index');
        }
		else{
			Session::flash('alert-danger', 'Please fill Captcha');
        	return redirect()->route('botman-answers.index');
        }
    }
}
