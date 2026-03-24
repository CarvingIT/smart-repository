<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Document;
use App\DocumentApproval;
use App\Approval;
use App\Collection;
use App\BinshopsPost;
//use App\BinshopsPublish;
use Session;

class ApprovalsController extends Controller
{
    //
	public function docApprovalForm(Request $request, $document_id)
    	{
            $document = \App\Document::find($document_id);
            if (!$document) abort(404);
            $collection_id = $document->collection_id;
	    $collection = \App\Collection::find($collection_id);
	    $doc_approvals = \App\DocumentApproval::all();

		$user_roles = [];
		foreach(auth()->user()->roles as $r){
			$user_roles[] = $r->role_id;
		}

		$current_approval = null;
		$approval_id = $request->query('approval_id');

		if(!empty($approval_id)){
			$current_approval = Approval::where('id', $approval_id)
				->where('approvable_id', $document_id)
				->where('approvable_type', 'App\\Document')
				->whereIn('approved_by_role', $user_roles)
				->first();
		}

		if(empty($current_approval)){
			$current_approval = Approval::where('approvable_id', $document_id)
				->where('approvable_type', 'App\\Document')
				->whereIn('approved_by_role', $user_roles)
				->orderBy('id', 'DESC')
				->first();
		}

		$current_approval_status = !empty($current_approval) ? $current_approval->approval_status : null;
		$current_approval_comments = !empty($current_approval) ? $current_approval->comments : '';

                return view('document_approval', [
								'collection' => $collection,
								'document' => $document,
								'doc_approvals' => $doc_approvals,
								'current_approval_status' => $current_approval_status,
								'current_approval_comments' => $current_approval_comments,
								'activePage' => 'Document Approval Form',
								'titlePage' => 'Document Approval'
							]);
        }

	public function saveApprovalStatus($approvable, $approvable_id, Request $request){
		$user_roles = [];
		foreach(auth()->user()->roles as $r){
			$user_roles[] = $r->role_id;
		}
		$approvable_type = ($approvable == 'blog')? 'App\BinshopsPost' : 'App\Document';
		$approval = Approval::where('approvable_id', $approvable_id)
			->where('approvable_type', $approvable_type)
			->whereIn('approved_by_role', $user_roles)
			->whereNull('approval_status')
			->orderBy('id', 'DESC')->first();

		if (!$approval) {
			Session::flash('alert-danger', 'No pending approval found for your role.');
			if($approvable=='blog'){
				return redirect('/en/'.$approvable.'/'.$request->slug);
			}
			return redirect('/'.$approvable.'/'.$approvable_id.'/approval');
		}

	   try{
		$approval->approved_by = auth()->user()->id;
		$approval->comments = $request->comments;
		$approval->approval_status = $request->approval_status;
		$approval->save();
		$this->nextApproval($approval);
	   	Session::flash('alert-success','Approval details have been saved successfully.');
	   }
	   catch(\Exception $e){
		Session::flash('alert-danger','There was an error. The operation could not be completed.'. $e->getMessage());
	   }
		if($approvable=='blog'){
	   return redirect('/en/'.$approvable.'/'.$request->slug);
		}
		else{
	   return redirect('/'.$approvable.'/'.$approvable_id.'/approval');
		}
	}

	public function nextApproval($approval_model){
		if($approval_model->approval_status != 1){
			// don't proceed if the current status is not approved
			return false;
		}

		$user_roles = [];
		foreach(auth()->user()->roles as $r){
			$user_roles[] = (int) $r->role_id;
		}

		if($approval_model->approvable_type == 'App\Document'){
			$collection_config = json_decode($approval_model->approvable->collection->column_config);
		}
		else{
			// this is for blog posts — use the approvable's collection or fall back to config
			$collection = Collection::find(config('app.blog_approval_collection_id', 1));
			if (!$collection) return false;
			$collection_config = json_decode($collection->column_config);
		}

		if (empty($collection_config) || empty($collection_config->approved_by)) return false;

		$last_approver_role =(int) end($collection_config->approved_by);
		if($approval_model->approved_by_role == $last_approver_role){
			// publish the approvable
			$approval_model->approvable->publish();
		}
		else{
			// send for next approval
			$index = array_search($approval_model->approved_by_role, $collection_config->approved_by);
			if ($index === false) {
				\Log::error('Approval role not found in chain: '.$approval_model->approved_by_role);
				return false;
			}
			$approver_roles = $collection_config->approved_by;
			$index++;
			if (!isset($approver_roles[$index])) {
				\Log::error('No next approver role defined at index '.$index);
				return false;
			}
			$next_approver_role = $approver_roles[$index];
			$new_approval = new Approval(['approved_by_role'=>$next_approver_role]);
			$approval_model->approvable->approvals()->save($new_approval);
		}
	}

	public function listByStatus($approvable, $status, Request $request){
		$user_roles = auth()->user()->roles;
		$roles_ar = [];
		foreach($user_roles as $r){
			$roles_ar[] = $r->role_id;
		}

		$list_items = Approval::whereIn('approved_by_role',$roles_ar);

		if($approvable == 'documents'){
			$list_items = $list_items->where('approvable_type','App\Document'); 
		}
		else if($approvable == 'blogs'){
			$list_items = $list_items->where('approvable_type','App\BinshopsPost'); 
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

//End of the class
}
