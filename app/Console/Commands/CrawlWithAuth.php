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

class CrawlWithAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:CrawlWithAuth {collection_id : ID of the collection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Authenticate and crawl';

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
        $elastic_hosts = env('ELASTIC_SEARCH_HOSTS', 'localhost:9200');
        $hosts = explode(",",$elastic_hosts);
        $elastic_client = ClientBuilder::create()->setHosts($hosts)->build();

        $url = \GuzzleHttp\Psr7\uri_for('http://www.carvingit.com');
	$crawl_handler = new \App\CrawlHandler();
	$crawl_handler->setCollectionId($collection_id);
	$crawl_handler->setElasticClient($elastic_client);

	/*
	 * Create a crawl client
	 * with crawl-client-options
	 */
	//$crawl_client_options = ['base_uri'=>'http://smart-repos.carvingit.com', 'cookies'=>true, 'allow_redirects'=>true];
	$crawl_client_options = ['base_uri'=>'http://www.carvingit.com', 'cookies'=>true, 'allow_redirects'=>true];
	$crawl_client = new GuzzleHttpClient($crawl_client_options);
		
	//$response = $crawl_client->request('GET', '/login');
	$response = $crawl_client->request('GET', '/user/login');
	$html_string = $response->getBody();
	//echo $html_string;
	$dom = new Dom;
	$dom->loadStr($html_string);
	$form = $dom->find('form')[0];
	$dom->loadStr($form->innerHtml);
	$inputs = $dom->find('input');
	$form_params = array();
	foreach($inputs as $input){
		$form_params[$input->name] = $input->value;
	}
	// post the data
	/*
	$form_params['email'] = 'ketan@carvingit.com';
	$form_params['password'] = 'ketan123';
	 */
	$form_params['name'] = 'carvingit';
	$form_params['pass'] = 'CarvingIT@123';

	//$response2 = $crawl_client->request('POST','/login',[
	$response2 = $crawl_client->request('POST','/user/login',[
		'form_params'=> $form_params
	]);
	//echo $response2->getBody();
	//exit;
	$crawler = new Crawler($crawl_client);
	$crawler
		//->setDelayBetweenRequests($sleep)
    		->setCrawlObserver($crawl_handler)
		->setCrawlProfile(new CrawlSubdomains($url))
    		->startCrawling($url);
    }
}
