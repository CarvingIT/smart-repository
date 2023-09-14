<?php

namespace App\Http\Controllers;
use \App\Http\Controllers\CollectionController;

use App\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use View;
use Session;
use App\DocumentApproval;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		if(env('VERIFY_EMAIL'))
        $this->middleware(['auth'=>'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
	//return redirect('/collections');
	$role_id = auth()->user()->userrole(auth()->user()->id);
	$collections=\App\Collection::where('require_approval','1')->get();
	$documents = [];
	foreach($collections as $collection){
		$config = $collection->getCollectionConfig();
		if(in_array($role_id, $config->approved_by)){	
			$documents[] = \App\Document::where('collection_id',$collection->id)->get();
		}
	}
	$count = $this->documentsAwaitingApprovalsCount();
	return view('dashboard',['collections'=>$collections, 'documents'=>$documents,'awaiting_count'=>$count]);
    }

	public function documentsAwaitingApprovalsCount(){
        // display documents which are not present in document_approvals table
                $all_docs = $approved_docs = $awaiting_approvals_docs = $awaiting_approvals_docs_role = [];
                $role_id= '';
           if(!empty(auth()->user()->userrole(auth()->user()->id))){
                $role_id = auth()->user()->userrole(auth()->user()->id);
	
                $collections=\App\Collection::where('require_approval','1')
				->get();

		if(!$collections->isEmpty()){
                   foreach($collections as $collection){
			$config = $collection->getCollectionConfig();
			if(in_array($role_id, $config->approved_by)){	
                		$all_docs[$collection->id] = \App\Document::where('collection_id',$collection->id)->get();
			}
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
	   }#if ends for role_id empty
		return count($awaiting_approvals_docs);
        }

}// End of the class
