<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Document;
use App\DocumentApproval;
use App\Collection;
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
	   $d_a->approved_by = auth()->user()->id;		
	   $d_a->approved_by_role = auth()->user()->userrole(auth()->user()->id);		
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

	public function documentsHandledByMe(Request $request){
	$user_id = $request->user_id;
	$role_id = auth()->user()->userrole($user_id);
        $collections=\App\Collection::where('column_config','LIKE','%'.$role_id.'%')->get();
	$status = '';
	if($request->status == 'approved'){
		$status = 1;
	}
	else{
		$status = 0;
	}
        $documents = DocumentApproval::where('approved_by_role',$role_id)
			->where('approval_status',$status)
			->orderBy('updated_at','DESC')->get();

        return view('docs_handled_by_me', ['documents'=>$documents, 'status'=>$status,'collections'=>$collections,
		'activePage'=>'Users Documents','titlePage'=>'Users Documents', 'title'=>'ISA Smart Repository']);
        }

	public function documentsAwaitingApprovals(Request $request){
	// Uploaded documents which are not presentin document_approvals table
		$role_id = auth()->user()->userrole(auth()->user()->id);
        	$collections=\App\Collection::where('column_config','LIKE','%'.$role_id.'%')->get();
        	$awaiting_approvals_docs = $document_ids = [];
        	foreach($collections as $collection){
        		$awaiting_approvals_docs[] = \App\Document::where('collection_id',$collection->id)->get();
        	}
        	$documents = DocumentApproval::where('approved_by_role',$role_id)->get();
		foreach($documents as $doc){
			$document_ids[] = $doc->document_id;
		}
        	return view('docs_awaiting_approvals',['collections'=>$collections, 'awaiting_approvals_docs'=>$awaiting_approvals_docs,
				'document_ids'=>$document_ids,
				'activePage'=>'Awaiting Approval Documents','titlePage'=>'Awaiting Approval Documents']);
	}


//End of the class
}
