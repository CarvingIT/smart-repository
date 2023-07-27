<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Elasticsearch\ClientBuilder;
use App\Synonyms;

class SetElasticSynonyms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ES:SetElasticSynonyms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds/updates synonyms to Elastic index - sr_documents';

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
		try{
		// create/update synonym file at /etc/elasticsearch/sr_synonyms.txt
		$synonyms_file_path = '/etc/elasticsearch/sr_synonyms.txt';

		$synonyms = Synonyms::all();
		$file_contents ='';
		foreach ($synonyms as $s){
			$file_contents .= $s->synonyms."\n";
		}

		file_put_contents($synonyms_file_path, $file_contents);

		// then reload the search analyzers
        $elastic_hosts = env('ELASTIC_SEARCH_HOSTS', 'localhost:9200');
        $hosts = explode(",",$elastic_hosts);
        	$client = ClientBuilder::create()->setHosts($hosts)->build();
			$params = ['index'=>'sr_documents'];
			//reload index
			$response = $client->indices()->reloadSearchAnalyzers($params);
			print_r($response);
		}
		catch(\Exception $e){
			print $e->getMessage()."\n";
		}
    }
}
