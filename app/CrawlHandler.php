<?php
namespace App;
use Spatie\Crawler\CrawlObserver;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use App\Url;

class CrawlHandler extends CrawlObserver{

   public function willCrawl(UriInterface $url) {
	//echo "Starting crawling $url\n";
   }

   public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null){
	//echo "Crawled $url\n";
	$status_code = $response->getStatusCode();
		if($status_code == '200'){
			$body = $response->getBody();
			$body->seek(0);
			$content = '';
			while($c = $body->read(1024)){
				$content .= $c;
			}
			$headers = $response->getHeaders();
			$url_model = new Url;
			$url_model->collection_id = 1; // this should be dynamic
			$url_model->url = (string)$url;
			$url_model->type = $headers['Content-Type'][0];
			$url_model->text_content = $this->getText($headers['Content-Type'][0], $content);
			$url_model->title = $this->getTitle($headers['Content-Type'][0], $content);
			$url_model->save();
		}
   }

   public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null){
	 echo "Failed crawling $url\n";
   }

   public function finishedCrawling() {
	  echo "Finished crawling\n";
   }

   private function getTitle($mime_type, $content){
	if(preg_match('/text\/html/', $mime_type)){
		$title = preg_match('/<title>(.*?)<\/title>/s', $content, $matches);
		return $matches[1];
	}
	else{
		return ''; // code needs to be handled for mime-types other than text/html
	}
   }

   private function getText($mime_type, $content){
	if(preg_match('/text\/html/', $mime_type)){
		$html = new \Html2Text\Html2Text($content);
		return $html->getText();
	}
	else{
		return ''; // code needs to be handled for mime-types other than text/html
	}
	/*
	$tmp_path = sys_get_temp_dir().'/'.\Uuid::generate()->string.'.'.$ext;
	$fh = fopen($tmp_path,'w+');
	fwrite($fh, $content);
	fclose($fh);
	$doc = new \App\DocXtract($tmp_path);
       	$text = $doc->convertToText();
	unlink($tmp_path);
	*/
   }
}
