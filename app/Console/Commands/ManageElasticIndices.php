<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Elastic\Elasticsearch\ClientBuilder;

class ManageElasticIndices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ES:ManageElasticIndices {operation : Operation to be performed viz create/delete } {index : Name of the index viz sr_documents}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Utility for managing Elastic Indices';

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
		$operation = $this->argument('operation');
		$index = $this->argument('index');

        $elastic_hosts = env('ELASTIC_SEARCH_HOSTS', 'localhost:9200');
        $hosts = explode(",",$elastic_hosts);
	$client = ClientBuilder::create()->setHosts($hosts)
		 ->setBasicAuthentication('elastic', env('ELASTIC_PASSWORD','some-default-password'))
                ->setCABundle('/etc/elasticsearch/certs/http_ca.crt')
		  ->build();
		
		if($operation == 'delete'){	
			$client->indices()->delete(['index'=>$index]);
		}
		else if($operation == 'create'){
			$params = ['index' => $index,
				'body'=>[
					'mappings'=>[
						'properties'=>[
							'sr_vector'=>[
								'type'=>"dense_vector",
								'dims'=> 5,
								'index'=> true,
								'similarity'=>'dot_product'
							],
							'title'=>[
								'type'=>'text',
								'fields'=>[
									'keyword'=>[
										'type'=>'keyword',
										'ignore_above'=>256
									]
								],
							],
							'text_content'=>[
								'type'=>'text',
								'fields'=>[
									'keyword'=>[
										'type'=>'keyword',
										'ignore_above'=>256
									]
								],
							],
						]
					]
				]			
			];
			$client->indices()->create($params);

			// add settings related to synonym analyzer
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
			$client->indices()->close(['index'=>'sr_documents']);
			$response = $client->indices()->putSettings($synonym_params);
			print_r($response);
			$client->indices()->open(['index'=>'sr_documents']);
		}
    }
}
