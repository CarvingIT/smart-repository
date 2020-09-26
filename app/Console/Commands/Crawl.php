<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlSubdomains;
use App\Collection;
use App\SpideredDomain;

class Crawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:Crawl {collection_id : ID of the collection}';

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

	$domains = SpideredDomain::where('collection_id', $collection_id)->get();

	foreach($domains as $d){
        $url = \GuzzleHttp\Psr7\uri_for($d->web_address);
	$crawl_handler = new \App\CrawlHandler();
	$crawl_handler->setCollectionId($collection_id);
	    Crawler::create()
    		->setCrawlObserver($crawl_handler)
		->setCrawlProfile(new CrawlSubdomains($url))
    		->startCrawling($url);
	}
    }
}
