<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DocumentSaved as DocumentSavedNotification;

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
		if(env('ENABLE_NOTIFICATIONS') == 1){
			// if a slack URL is provided, notifiable = collection else users of the collection
			$collection_config = json_decode($event->document->collection->column_config);
			if(!empty($collection_config->slack_webhook)){
				$notifiable = $event->document->collection;
			}
			else{
				$notifiable = $event->document->collection->getUsers();
			}
			//Log::debug($collection_users);
			try{
				Notification::send($notifiable, new DocumentSavedNotification($event->document));
			}
			catch(\Exception $e){
				Log::error($e->getMessage());
			}
		}

	    // Update elasticsearch index
	    $elastic_hosts = env('ELASTIC_SEARCH_HOSTS', 'localhost:9200');
	    $hosts = explode(",",$elastic_hosts);
	    $client = ClientBuilder::create()->setHosts($hosts)->build();
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
}
