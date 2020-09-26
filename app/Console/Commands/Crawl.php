<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlSubdomains;

class Crawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:Crawl';

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
	    $url = \GuzzleHttp\Psr7\uri_for('http://firstray.in');
	    Crawler::create()
    		->setCrawlObserver(new \App\CrawlHandler())
		->setCrawlProfile(new CrawlSubdomains($url))
    		->startCrawling($url);
    }
}
