<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlSubdomains;
use App\Collection;
use App\SpideredDomain;
use Elasticsearch\ClientBuilder;

class Crawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
	protected $signature = 'SR:Crawl {collection_id : ID of the collection}
				{--site= : Optional. Crawl only this site for this collection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl an external URL for indexing';

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
        echo "Crawling domains of ".$c->name."\n";
        $site = $this->option('site');

	if(empty($site)){
	$domains = SpideredDomain::where('collection_id', $collection_id)->get();
		foreach($domains as $d){
			$this->crawlSite($collection_id, $d);
		}
	}
	else{ // $site is not empty
		$this->crawlSite($collection_id, $site);
	}
    }

    private function crawlSite($collection_id, $site_address){
        $elastic_hosts = env('ELASTIC_SEARCH_HOSTS', 'localhost:9200');
        $hosts = explode(",",$elastic_hosts);
        $client = ClientBuilder::create()->setHosts($hosts)->build();

        $url = \GuzzleHttp\Psr7\uri_for($site_address);
	$crawl_handler = new \App\CrawlHandler();
	$crawl_handler->setCollectionId($collection_id);
	$crawl_handler->setCrawlClient($client);

	    Crawler::create()
    		->setCrawlObserver($crawl_handler)
		->setCrawlProfile(new CrawlSubdomains($url))
    		->startCrawling($url);
    }
}