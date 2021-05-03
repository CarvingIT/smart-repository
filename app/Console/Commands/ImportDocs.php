<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Elasticsearch\ClientBuilder;

class ImportDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:ImportDocs {collection_id : ID of the collection} 
                {--dir= : Full path of the directory containing the documents to be imported} 
                {--csv= : Full path of the CSV file containing file paths and meta data }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import files into a collection that is already created.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $elastic_hosts = env('ELASTIC_SEARCH_HOSTS', 'localhost:9200');
        $hosts = explode(",",$elastic_hosts);
        $client = ClientBuilder::create()->setHosts($hosts)->build();

		// Is elastic search running ?
		// get the indices and see if an error is returned
		$params = [ 'index' => 'sr_documents', 
					    'body'  => [
    					    'query' => [
            					'match' => [
                					'testField' => 'abc'
            					]
        					]
    					]
				];
		try{
			$es_on = true;
			$results = $client->search($params);
		}
		catch(\Elasticsearch\Common\Exceptions\NoNodesAvailableException $e){
			echo "WARNING: ElasticSearch nodes are not available. When the service starts, re-indexing will have to be done.\n";
			$es_on = false;
		}

        $collection_id = $this->argument('collection_id');
        $dir = $this->option('dir');
        $csv = $this->option('csv');
        //echo "$collection_id  $dir  $csv\n";
        if(empty($dir) && empty($csv)){
            echo "Aborting. One of --dir or --csv must be specified.\n";
        }
        if($dir){
            //list the directory and take each file path in array
            $list = scandir($dir);
            foreach($list as $f){
                if(is_file($dir.'/'.$f)){
                   	$d = \App\Http\Controllers\DocumentController::importFile($collection_id, $dir.'/'.$f);
                   	echo $dir.'/'.$f."\n";
	    			// Update elastic index
					if($es_on){
                    try{
            	    	$body = $d->toArray();
		   		    	$body['collection_id'] = $collection_id;
            			$params = [
                			'index' => 'sr_documents',
                			'id'    => $d->id,
                			'body'  => $body
            			];

            			$response = $client->index($params);
            			print_r($response);
                    }
                    catch(\Exception $e){
                    	echo "!! Error while importing $f\n";
                    	echo $e->getMessage()."\n";
                    }
				  } // if ES is on
                }
            }
        }
        else if($csv){

        }
    }
}
