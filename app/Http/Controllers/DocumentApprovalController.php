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
	// documents which are not present in document_approvals table
        	$all_docs = $approved_docs = $awaiting_approvals_docs = $document_ids = [];
		$role_id = auth()->user()->userrole(auth()->user()->id);
        	$collections=\App\Collection::where('column_config','LIKE','%'.$role_id.'%')->get();

		foreach($collections as $collection){
		$role_sequence[$collection->id] = json_decode($collection->column_config)->approved_by;
        	$all_docs[] = \App\Document::where('collection_id',$collection->id)->get();
		}
//print_r($role_sequence);
foreach($role_sequence as $s => $v){
		foreach($all_docs as $doc){
			foreach($doc as $d){
				$role_index = array_search($role_id,$v);
				if($role_index == 0){// Display documents when the first role assigned in collection settings, user is loggedin. (principal)
        				$approved_docs = DocumentApproval::where('approved_by_role',$v[0])
					->where('document_id',$d->id)
					->get();
					if($approved_docs->isEmpty())
					{
					$awaiting_approvals_docs[] = $d;
					}
					else{ continue;}
				}
				else{ //Display documents approved orunapproved by previous role.
					$previous_role_index = $role_index-1;
        				$approved_docs = 
					//DocumentApproval::where('approved_by_role',$v[$previous_role_index])
					DocumentApproval::
					where('document_id',$d->id)
					->orderByDesc('id')
					->first();
//print_r($approved_docs);
					//if(!$approved_docs->isEmpty())
					if(!empty($approved_docs) && $approved_docs->approved_by_role <= $role_index)
					{
//echo $role_index; 
					$awaiting_approvals_docs[] = $d;
					}
				}
				//echo "Role Index ".$role_index." ".$i."<br />";
			}
		}
}
$awaiting_approvals_docs = array_unique($awaiting_approvals_docs);

//exit;

        	return view('docs_awaiting_approvals',['collections'=>$collections, 'awaiting_approvals_docs'=>$awaiting_approvals_docs,
				'activePage'=>'Awaiting Approval Documents','titlePage'=>'Awaiting Approval Documents']);
	}


//End of the class
}
