<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Elasticsearch\ClientBuilder;

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
    }
}
