<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Elasticsearch\ClientBuilder;

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
	    Log::info('Document saved: '.$event->document->id);
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
