<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Document;
use App\Collection;
use Elasticsearch\ClientBuilder;

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
        $c = Collection::find($collection_id);
        echo "Rebuilding elastic index of ".$c->name."\n";
        
        $elastic_hosts = env('ELASTIC_SEARCH_HOSTS', 'localhost:9200');
        $hosts = explode(",",$elastic_hosts);
        $client = ClientBuilder::create()->setHosts($hosts)->build();

        $docs = $c->documents;
        foreach($docs as $d){
            echo $d->title."\n";
            $params = [
                'index' => 'sr_documents',
                'id'    => $d->id,
                'body'  => ['collection_id'=>$c->id, 'text_content' => $d->text_content]
            ];

            $response = $client->index($params);
            print_r($response);
        }
    }
}
