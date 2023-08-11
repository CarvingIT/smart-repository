<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Document;
use App\DocumentApproval;
use Session;

class DocumentApprovalController extends Controller
{
    //
	public function docApprovalForm($document_id)
    	{
            $document = \App\Document::find($document_id);
            $collection_id = $document->collection_id;
	    $collection = \App\Collection::find($collection_id);
	    $doc_approvals = \App\DocumentApproval::all();
                return view('document_approval', ['collection'=>$collection, 'document'=>$document,'doc_approvals'=>$doc_approvals,
                                'activePage'=>'Document Approval Form','titlePage'=>'Document Approval']);
        }

	public function saveApprovalStatus(Request $request){
	   $d_a = new DocumentApproval();
	   $d_a->document_id = $request->document_id;
	   $d_a->approved_by = Auth()->user()->id;		
	   $d_a->approval_status = $request->approval_status;		
	   $d_a->comments = $request->comments;		
	   try{
	   $d_a->save();
	   	Session::flash('alert-success','Document approval details have been saved successfully.');
	   }
	   catch(\Exception $e){
		Session::flash('alert-danger','Error has orrcured: Please try again. '.$e->getMessage());
	   }
	   return redirect('/document/'.$request->document_id.'/approval');
	}

//End of the class
}
