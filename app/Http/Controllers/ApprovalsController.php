<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Document;
use App\DocumentApproval;
use App\Approval;
use App\Collection;
use Session;

class ApprovalsController extends Controller
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

	public function saveApprovalStatus($approvable, $approvable_id, Request $request){
	   if($approvable == 'document'){
			$document = Document::find($approvable_id);
			$approval = $document->approvals
				->sortByDesc('id')->first();
	   }
	   try{
		$approval->approved_by = auth()->user()->id;
		$approval->comments = $request->comments;
		$approval->approval_status = $request->approval_status;
		$approval->save();
	   	Session::flash('alert-success','Approval details have been saved successfully.');
	   }
	   catch(\Exception $e){
		Session::flash('alert-danger','Error has orrcured: Please try again. '.$e->getMessage());
	   }
	   return redirect('/'.$approvable.'/'.$approvable_id.'/approval');
	}

	public function listByStatus($approvable, $status, Request $request){
		$user_roles = auth()->user()->roles;
		$roles_ar = [];
		foreach($user_roles as $r){
			$roles_ar[] = $r->id;
		}

		$list_items = Approval::whereIn('approved_by_role',$roles_ar);

		if($approvable == 'documents'){
			$list_items = $list_items->where('approvable_type','App\Document'); 
		}
		else if($approvable == 'blogs'){
			$list_items = $list_items->where('approvable_type','App\BinshopPost'); 
		}

		if($status == 'approved'){
			$list_items = $list_items->where('approval_status', 1);
		}
		else if($status == 'rejected'){
			$list_items = $list_items->where('approval_status', 0);
		}
		else{
			$list_items = $list_items->whereNull('approval_status');
		}

		$list_items = $list_items->orderBy('updated_at','DESC')->get();
        return view('approvables_list', ['approvables'=>$list_items, 
			'status'=>$status,
			]);
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
