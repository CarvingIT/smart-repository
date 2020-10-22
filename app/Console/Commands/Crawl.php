<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlSubdomains;
use App\Collection;
use App\SpideredDomain;
use Elasticsearch\ClientBuilder;
use GuzzleHttp\Client as GuzzleHttpClient;
use PHPHtmlParser\Dom;
//use Spatie\Browsershot\Browsershot;

class Crawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
	protected $signature = 'SR:Crawl {collection_id : ID of the collection}
				{--site= : Optional. Crawl only this site for this collection}
				{--sleep=1000 : Optional. Sleep n milliseconds between two http requests}';

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
	$sleep = $this->option('sleep');

	if(empty($site)){
	$domains = SpideredDomain::where('collection_id', $collection_id)->get();
		foreach($domains as $d){
		
		/*
		Client options that need to be enabled/added
		allow_redirects = true
		cookies = true 
		also need to set auth information
	 	*/
			$crawl_client_options = ['base_uri'=> $d->web_address, 'cookies'=>true, 'allow_redirects'=>true];
			$crawl_client = new GuzzleHttpClient($crawl_client_options);
			$crawler = new Crawler($crawl_client);

			if(!empty($d->auth_info)){// auth info is not empty
				// create a guzzle http client and use that after authentication
				// more code needed here
				$auth_info = json_decode($d->auth_info);
				print_r($auth_info);
				$response = $crawl_client->request('GET', $auth_info->entry_url);
				$html_string = $response->getBody();
				$dom = new Dom;
				$dom->loadStr($html_string);
				$dom_identifier = $auth_info->login_form_dom_identifier;
				$form_number = $auth_info->form_element_number;
				$form = $dom->find($dom_identifier)[$form_number];
				$dom->loadStr($form->innerHtml);
				$inputs = $dom->find('input');
				$form_params = array();
				foreach($inputs as $input){
					$form_params[$input->name] = $input->value;
				}
				// post the data
				foreach($auth_info->inputs as $input){
					$form_params[$input->name] = $input->value;
				}
				//$response2 = $crawl_client->request('POST','/login',[
				$response2 = $crawl_client->request(
					$auth_info->login_form_method,
					$auth_info->login_form_action,[
					'form_params'=> $form_params
				]);
			}

			$this->crawlSite($collection_id, $d->web_address, $crawler, $sleep);
		}
	}
	else{ // $site is not empty which is passed from the command line
		$crawl_client_options = ['base_uri'=> $site, 'cookies'=>true, 'allow_redirects'=>true];
		$crawl_client = new GuzzleHttpClient($crawl_client_options);
		$crawler = new Crawler($crawl_client);
		$this->crawlSite($collection_id, $site, $crawler, $sleep);
	}
    }

    private function crawlSite($collection_id, $site_address, $crawler, $sleep){
        $url = \GuzzleHttp\Psr7\uri_for($site_address);
	$crawl_handler = new \App\CrawlHandler($collection_id);

	    //Crawler::create()
		$crawler
		->setDelayBetweenRequests($sleep)
    		->setCrawlObserver($crawl_handler)
		->setCrawlProfile(new CrawlSubdomains($url))
		//->executeJavaScript()
    		->startCrawling($url);
    }
}
