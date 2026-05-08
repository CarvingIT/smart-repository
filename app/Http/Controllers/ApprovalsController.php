<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Document;
use App\DocumentApproval;
use App\Approval;
use App\Collection;
use App\BinshopsPost;
use App\Role;
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
	    $document->load(['approvals.approver', 'approvals.approver_role']);
	    $collection_config = json_decode($collection->column_config) ?? new \stdClass();
	    $workflow_roles = array_values((array) ($collection_config->approved_by ?? []));
	    $workflow_checklists = array_values((array) ($collection_config->approval_checklist_labels ?? []));
	    $role_models = Role::whereIn('id', $workflow_roles)->get()->keyBy('id');

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
				->whereNull('approval_status')
				->first();
		}

		if(empty($current_approval)){
			$current_approval = Approval::where('approvable_id', $document_id)
				->where('approvable_type', 'App\\Document')
				->whereIn('approved_by_role', $user_roles)
				->whereNull('approval_status')
				->orderBy('id', 'DESC')
				->first();
		}

		$current_approval_status = !empty($current_approval) ? $current_approval->approval_status : null;
		$current_approval_comments = !empty($current_approval) ? $current_approval->comments : '';
		$current_approval_checklist_values = !empty($current_approval) && is_array($current_approval->checklist_values)
			? array_values($current_approval->checklist_values)
			: [];

		$approval_workflow_stages = [];
		foreach ($workflow_roles as $workflow_index => $workflow_role_id) {
			$workflow_role_id = (int) $workflow_role_id;
			$stage_approval = $document->approvals->first(function ($approval) use ($workflow_role_id) {
				return (int) $approval->approved_by_role === $workflow_role_id;
			});
			$stage_labels = $workflow_checklists[$workflow_index] ?? [];
			if (!is_array($stage_labels)) {
				$stage_labels = [];
			}

			$approval_workflow_stages[] = [
				'role_id' => $workflow_role_id,
				'role_name' => optional($role_models->get($workflow_role_id))->name ?? ('Role '.$workflow_role_id),
				'labels' => $stage_labels,
				'approval' => $stage_approval,
				'is_current' => !empty($current_approval) && (int) $current_approval->approved_by_role === $workflow_role_id,
				'is_editable' => !empty($current_approval) && (int) $current_approval->approved_by_role === $workflow_role_id && is_null($current_approval->approval_status),
			];
		}

                return view('document_approval', [
								'collection' => $collection,
								'document' => $document,
								'current_approval' => $current_approval,
								'current_approval_status' => $current_approval_status,
								'current_approval_comments' => $current_approval_comments,
								'current_approval_checklist_values' => $current_approval_checklist_values,
								'approval_workflow_stages' => $approval_workflow_stages,
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
		$approval_id = $request->input('approval_id');
		$approval = null;
		if (!empty($approval_id)) {
			$approval = Approval::where('id', $approval_id)
				->where('approvable_id', $approvable_id)
				->where('approvable_type', $approvable_type)
				->whereIn('approved_by_role', $user_roles)
				->whereNull('approval_status')
				->first();
		}

		if (!$approval) {
			$approval = Approval::where('approvable_id', $approvable_id)
				->where('approvable_type', $approvable_type)
				->whereIn('approved_by_role', $user_roles)
				->whereNull('approval_status')
				->orderBy('id', 'DESC')->first();
		}

		if (!$approval) {
			Session::flash('alert-danger', 'No pending approval found for your role.');
			if($approvable=='blog'){
				return redirect('/en/'.$approvable.'/'.$request->slug);
			}
			return redirect('/'.$approvable.'/'.$approvable_id.'/approval');
		}

	   try{
		$approval->load('approvable.collection');
		$collection_config = json_decode(optional($approval->approvable->collection)->column_config) ?? new \stdClass();
		$workflow_roles = array_values((array) ($collection_config->approved_by ?? []));
		$workflow_checklists = array_values((array) ($collection_config->approval_checklist_labels ?? []));
		$workflow_role_index = array_search((int) $approval->approved_by_role, array_map('intval', $workflow_roles), true);
		$current_stage_labels = ($workflow_role_index !== false && isset($workflow_checklists[$workflow_role_index]) && is_array($workflow_checklists[$workflow_role_index]))
			? $workflow_checklists[$workflow_role_index]
			: [];
		$submitted_checklist_values = (array) $request->input('checklist_values', []);
		$normalized_checklist_values = [];
		foreach ($current_stage_labels as $checklist_index => $checklist_label) {
			$normalized_checklist_values[$checklist_index] = !empty($submitted_checklist_values[$checklist_index]) ? 1 : 0;
		}
		$approval->checklist_values = $normalized_checklist_values;
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
