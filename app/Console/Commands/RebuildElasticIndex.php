<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Document;
use App\Collection;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;

class RebuildElasticIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ES:RebuildElasticIndex {collection_id : ID of the collection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuilds Elastic Index';

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
        $collection_id = $this->argument('collection_id');
        if($collection_id == 'all'){
            $collections = Collection::all();
            foreach($collections as $c){
                $this->indexCollection($c);
            }
        }
        else{
            $c = Collection::find($collection_id);
            $this->indexCollection($c);
        }
    }

    public function indexCollection($c){
        echo "Rebuilding elastic index of ".$c->name."\n";
	    if($c->content_type == 'Uploaded documents'){
		$index = 'sr_documents';
        	$docs = $c->documents;
	    }
	    else if($c->content_type == 'Web resources'){
		$index = 'sr_urls';
        	$docs = $c->urls;
	    }
        
        $elastic_hosts = env('ELASTIC_SEARCH_HOSTS', 'localhost:9200');
        $hosts = explode(",",$elastic_hosts);
    	$client = ClientBuilder::create()->setHosts($hosts)
		->setBasicAuthentication('elastic', env('ELASTIC_PASSWORD','some-default-password'))
		->setCABundle('/etc/elasticsearch/certs/http_ca.crt')
		->build();
	    // first, clear the old records from the index
        $delete_params = [
                'index' => $index,
                'body'=>[
                    'query'=>[
                        'match'=>[
                            'collection_id' => $c->id
                        ]
                    ]
                ]
            ];
        $delete_response = $client->deleteByQuery($delete_params);
        foreach($docs as $d){
            $body = $d->toArray();
            $body['text_content'] = $d->text_content;
            foreach($d->meta as $mv){
                if(!empty($mv->value)){
                    if($mv->meta_field->type == 'Numeric') {
                        $val = floatval($mv->value);
                    }
                    else {// for Date/Text/Textarea/Select/....
                        $val = $mv->value;
                    }
                    $body['meta_'.$mv->meta_field_id] = $val;
                }
            }
            $params = [
                'index' => $index,
                'id'    => $d->id,
                'body'  => $body
            ];

            //echo $d->id."\t".$d->title."\n";
            echo '.';
            try{
                $response = $client->index($params);
            }
            catch(\Exception $e){
                echo $e->getMessage()."\n";
                echo json_encode($params);
                echo "\nCould not index record - ".$d->id."\n"; 
            }
            //print_r($response);
        }
		$client->indices()->close(['index'=>'sr_documents']);
		$client->indices()->open(['index'=>'sr_documents']);
    }
}
		// add settings related to synonym analyzer
		/*
		$synonym_params = [
			'index' => 'sr_documents',
   			'body' => [
       			'settings' => [
       				'number_of_replicas' => 0,
       				'refresh_interval' => -1,
					'analysis' => [
						'analyzer' => [
							'synonyms_analyzer' => [
								'tokenizer' => 'standard',
									'filter' => [
										'lowercase',
										'sr_synonyms'
									]
							]
						],
						'filter' => [
							'sr_synonyms' => [
								'type' => 'synonym',
								'synonyms_path' => '/etc/elasticsearch/sr_synonyms.txt',
								'updateable' => true
							]
						]
					]
       			]
   			]
		];
		$response = $client->indices()->putSettings($synonym_params);
		print_r($response);
		*/
