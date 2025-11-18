<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DocumentDeleted as DocumentDeletedNotification;

class DocumentDeleted
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
        // if a slack URL is provided, notifiable = collection else users of the collection
        $collection_config = json_decode($event->document->collection->column_config);
        if(!empty($collection_config->slack_webhook) || !empty($collection_config->notify_email)){
            $notifiable = $event->document->collection;
            //Log::debug($collection_users);
            try{
                Notification::send($notifiable, new DocumentDeletedNotification($event->document));
            }
            catch(\Exception $e){
                Log::error($e->getMessage());
            }
        }

	    Log::info('Document deleted: '.$event->document->id); 
	    // Update elasticsearch index
	    $elastic_hosts = env('ELASTIC_SEARCH_HOSTS', 'localhost:9200');
	    $hosts = explode(",",$elastic_hosts);
	    $client = ClientBuilder::create()->setHosts($hosts)->build();

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

        $document = $event->document;
        $collection = $document->collection;

        if (!$collection) {
            return; 
        }

        $document_size = (int)$document->size;
        $revisions_size = (int) \App\DocumentRevision::where('document_id', $document->id)
            ->sum('size');

        if ($document->deleted_at !== null){
            $collection->decrement("document_count");

            if ($document_size > 0) {
                $collection->decrement("size_active", $document_size);
            }

            if ($revisions_size > 0) {
                $collection->decrement("size_revisions", $revisions_size);
                $collection->increment("size_deleted", $revisions_size);
            }
        }
    }
}
