<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Elasticsearch\ClientBuilder;

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
        $client = ClientBuilder::create()->setHosts($hosts)->build();
		
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
								'type'=>'keyword'
							],
							'text_content'=>[
								'type'=>'keyword'
							],
						]
					]
				]			
			];
			$client->indices()->create($params);
		}
    }
}