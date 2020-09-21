<?php
namespace App;
use Spatie\Crawler\CrawlObserver;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class CrawlHandler extends CrawlObserver{

   public function willCrawl(UriInterface $url) {
	echo "Starting crawling $url\n";
   }

   public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null){
	echo "Crawled $url\n";
   }

   public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
   ){
	 echo "Failed crawling $url\n";
   }

    public function finishedCrawling() {
	echo "Finished crawling\n";
    }
}
