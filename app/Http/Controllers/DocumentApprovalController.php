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
	   $collection = \App\Collection::find($request->collection_id);
	   $collection_role_details = json_decode($collection->column_config);
	   $top_user_role_array = $collection_role_details->approved_by;
           $top_user_role = array_pop($top_user_role_array);	
//echo $top_user_role;
//print_r($collection_role_details); exit;
	
	   $user_role = auth()->user()->userrole(auth()->user()->id);
	   $document_id = $request->document_id;
	   $approval = DocumentApproval::where('document_id',$request->document_id)
				->where('approved_by_role',$user_role)
				->first();
	   if(empty($approval)){
	   $d_a = new DocumentApproval();
	   }
	   else{
	   $d_a = DocumentApproval::find($approval->id);
	   }
	   $d_a->document_id = $request->document_id;
	   $d_a->approved_by = auth()->user()->id;		
	   $d_a->approved_by_role = $user_role;		
	   $d_a->approval_status = $request->approval_status;		
	   $d_a->comments = $request->comments;		
	   try{
	   $d_a->save();
	   	$document_approval = DocumentApproval::where('document_id',$document_id)
				->where('approved_by_role','=',$top_user_role)
				->where('approval_status','=',1)
				->first();
		if(!empty($document_approval)){
			$document_details = Document::find($request->document_id);
			$document_details->approved_by = auth()->user()->userrole(auth()->user()->id);
			$document_details->approved_on = now();
			$document_details->save();
		}
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
		// display documents which are not present in document_approvals table
        	$all_docs = $approved_docs = $awaiting_approvals_docs = $awaiting_approvals_docs_role = [];
		$collections = $role_id = '';
	   if(!empty(auth()->user()->userrole(auth()->user()->id))){
		$role_id = auth()->user()->userrole(auth()->user()->id);
        	$collections=\App\Collection::where('require_approval','1')->get();

		foreach($collections as $collection){
		$config = $collection->getCollectionConfig();
		  if(in_array($role_id,$config->approved_by)){
        		$all_docs[$collection->id] = \App\Document::where('collection_id',$collection->id)->get();
		  }
		}

		foreach($all_docs as $collection_id => $doc){
		$collection_role_sequence = \App\Collection::find($collection_id);
		$role_sequence[$collection->id] = json_decode($collection_role_sequence->column_config)->approved_by;
			foreach($role_sequence as $s => $v){
				$role_index = array_search($role_id,$v);
				foreach($doc as $d){
				if($role_index == 0){
				//Display documents when the first role assigned in collection settings, user is loggedin. (principal)
        				$approved_docs = DocumentApproval::where('approved_by_role',$v[$role_index])
					->where('document_id',$d->id)
					->first();
					if(empty($approved_docs))
					{
					$awaiting_approvals_docs[] = $d;
					//echo "Role Index Internal\n";print_r($awaiting_approvals_docs_role); 
					}
					elseif(!empty($approved_docs) && $approved_docs->approval_status == 0){
					$awaiting_approvals_docs[] = $d;
					}
					else{ continue;}
				}
				else{ //Display documents handled by previous role.
					$previous_role_index = $role_index-1;
        				$approved_docs = DocumentApproval::where('document_id',$d->id)
							->orderByDesc('id')
							->first();
					if(!empty($approved_docs) && $approved_docs->approved_by_role == $v[$previous_role_index] && $approved_docs->approval_status == 1)
					{
					$awaiting_approvals_docs[] = $d;
					}
					elseif(!empty($approved_docs) && $approved_docs->approved_by_role == $v[$role_index] && $approved_docs->approval_status == 0){
					$awaiting_approvals_docs[] = $d;
					}
					else{continue;}
				}
				}
			}
		}
		$awaiting_approvals_docs = array_unique($awaiting_approvals_docs);
	    }# if ends for role_id empty
        	return view('docs_awaiting_approvals',['collections'=>$collections, 'awaiting_approvals_docs'=>$awaiting_approvals_docs,
				'activePage'=>'Awaiting Approval Documents','titlePage'=>'Awaiting Approval Documents']);
	}


//End of the class
}
