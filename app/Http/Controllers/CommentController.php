<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DocumentComment;
use Session;

class CommentController extends Controller
{
    //
	public function save(Request $request){
	$comment = new \App\DocumentComment;
        $comment->document_id = $request->document_id;
        $comment->collection_id = $request->collection_id;
        $comment->created_by = empty(\Auth::user()->id)? null : \Auth::user()->id;
	$comment->comment = $request->comment;
	try{
        	$comment->save();
                Session::flash('alert-success', 'Comment posted successfully!');
            }
            catch(\Exception $e){
                Session::flash('alert-danger', 'Error: '.$e->getMessage());
            }

	return redirect("/collection/".$request->collection_id."/document/".$request->document_id."/details");

	}
}
