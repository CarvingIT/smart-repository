<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DocumentSaved as DocumentSavedNotification;
use App\Approval;

class DocumentSaved
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $changes = $event->document->getChanges();
        if(count($changes) == 2 && in_array('locked', array_keys($changes))){
            // ignore; since only updated_at and locked status was changed
            return 0;
        }        

		$notifiable = $event->document->collection;
        $collection_config = json_decode($event->document->collection->column_config);
        if(!empty($collection_config->slack_webhook) || !empty($collection_config->notify_email)){
		    try{
		    	Notification::send($notifiable, new DocumentSavedNotification($event->document));
		    }
		    catch(\Exception $e){
		    	Log::error($e->getMessage());
		    }
        }

	    // Update elasticsearch index 
		// if collection requires approval and the document is not approved, don't update the elastic index
		// also attempt to remove this particular record from elastic index
       	$elastic_hosts = env('ELASTIC_SEARCH_HOSTS', 'localhost:9200');
       	$hosts = explode(",",$elastic_hosts);
       	$client = ClientBuilder::create()->setHosts($hosts)->build();
		if($event->document->collection->require_approval == 1 && 
			empty($event->document->approved_on)){
			// don't update the elastic index
			// remove the record
        	$params = [
            	'index'=>'sr_documents',
            	'id'=>$event->document->id
        	];
        	try{
        	$response = $client->delete($params);
        	Log::info('Removed document from Elastic index', $response);
        	}
        	catch(\Exception $e){
            	Log::warning($e->getMessage());
        	}
		}
		else{
            $body = $event->document->toArray();
            $body['collection_id'] = $event->document->collection->id;
            $params = [
                'index' => 'sr_documents',
                'id'    => $event->document->id,
                'body'  => $body
            ];
	    	try{
            	$response = $client->index($params);
	    		Log::info('Elastic index updated', $response);
	    	}
	    	catch(\Exception $e){
	    		Log::warning($e->getMessage());
	    	}
		}

		// add a record in the approvals table
		if($event->document->collection->require_approval == 1){
			// get the first role id from approval workflow
			$collection_config = $event->document->collection->column_config;	
			$col_conf = json_decode($collection_config);
			$approvers = $col_conf->approved_by;
			$approval_record = new Approval(['approved_by_role'=>$approvers[0]]);
			$event->document->approvals()->save($approval_record);
		}
    }
}
