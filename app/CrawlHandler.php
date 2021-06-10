<?php
namespace App;
use Spatie\Crawler\CrawlObserver;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use App\Url;
use App\UrlSuppression;
use App\DesiredUrl;
use Elasticsearch\ClientBuilder;

class CrawlHandler extends CrawlObserver{
   var $collection_id = null;
   var $elastic_client = null;
   var $indexing_errors = array();

   public function __construct($collection_id){
	   $this->collection_id = $collection_id;
	   $this->setElasticClient();
   }

   private function setElasticClient(){
        $elastic_hosts = env('ELASTIC_SEARCH_HOSTS', 'localhost:9200');
        $hosts = explode(",",$elastic_hosts);
        $this->elastic_client = ClientBuilder::create()->setHosts($hosts)->build();
   }

   public function willCrawl(UriInterface $url) {
	//echo "Starting crawling $url\n";
   }

   public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null){
	// check restrictions 
	   // do not save if it does not match desired_urls or is in suppression list
	   if(!$this->canSave($url)){
		   //echo "Suppressing ".(string) $url.'.';
		   return null;
	   }

	$status_code = $response->getStatusCode();
		if($status_code == '200'){
			$body = $response->getBody();
			$body->seek(0);
			$content = '';
			while($c = $body->read(1024)){
				$content .= $c;
			}
			try{
			$headers = $response->getHeaders();
			$content_type_ar = explode(";",$headers['Content-Type'][0]);
			$mime_type = $content_type_ar[0];
			$url = (string)$url;
			$url_query = Url::where('collection_id', $this->collection_id)->where('url',$url);
			$url_model = ($url_query->count() == 1) ? $url_query->first() : new Url;
			$url_model->collection_id = $this->collection_id; 
			$url_model->url = $url;
			$url_model->type = $mime_type;
			$url_model->text_content = $this->getText($mime_type, $content);
			$url_model->title = $this->getTitle($mime_type, $content);
			$url_model->raw_content = $content;
			$url_model->size = strlen($content);
			$url_model->save();
		    	// Update elastic index
            	   	$body = $url_model->toArray();
		   	$body['collection_id'] = $this->collection_id;
            			$params = [
                			'index' => 'sr_urls',
                			'id'    => $url_model->id,
                			'body'  => $body
            			];

            		$response = $this->elastic_client->index($params);
            		print_r($response);
			}
			catch(\Exception $e){
				// log error to a log file instead of to the standard output
				if(!in_array($e->getCode(), $this->indexing_errors)){
					echo $e->getCode()." : ";
					echo "WARNING: ".$e->getMessage()."\n"; 
					$this->indexing_errors[] = $e->getCode();
				}
			}
		}
   }

   public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null){
	 echo "Failed crawling $url\n";
   }

   public function finishedCrawling() {
	  echo "Finished crawling\n";
   }

   private function getTitle($mime_type, $content){
	if($mime_type == 'text/html'){
		$title = preg_match('/<title>(.*?)<\/title>/s', $content, $matches);
		return $matches[1];
	}
	else{
		$text = $this->getText($mime_type, $content);
		$title = strtok($text, "\n");
		return $title; // code needs to be handled for mime-types other than text/html
	}
   }

   private function getText($mime_type, $content){
	try{
	if($mime_type == 'text/html'){
		$html = new \Html2Text\Html2Text($content);
		return $html->getText();
	}
	else{
		$ext = $this->getFileExtension($mime_type);
		if(!empty($ext)){
			$tmp_path = sys_get_temp_dir().'/'.\Uuid::generate()->string.'.'.$ext;
			$fh = fopen($tmp_path,'w+');
			fwrite($fh, $content);
			fclose($fh);
			$text = $this->extractText($tmp_path, $mime_type);
			unlink($tmp_path);
			return $text;
		}
		return ''; // default; if $ext is empty 
	}
	}
	catch(\Exception $e){
		// log error to a log file instead of to the standard output
		echo "ERROR: ".$e->getMessage()."\n"; 
	}
   }

   private function getFileExtension($mime_type){
	$extensions = array('image/jpeg' => 'jpg',
		'image/png'=>'png',
		'image/gif'=>'gif',
		'application/vnd.ms-powerpoint'=>'ppt',
		'text/xml' => 'xml',
		'application/pdf'=>'pdf',
		'text/plain'=>'txt',
		'application/vnd.oasis.opendocument.text'=>'odt',
		'application/vnd.oasis.opendocument.spreadsheet'=>'ods',
		'application/msword'=>'doc',
		'application/vnd.ms-excel'=>'xls',
		'application/rtf'=>'rtf',
        );
	if(empty($extensions[$mime_type])) return '';
	return $extensions[$mime_type];
   }

   private function extractText($path, $mime_type){
	$text = '';
        if($mime_type == 'application/pdf'){
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($path);
            $text = $pdf->getText();
            $text = str_replace(array('&', '%', '$'), ' ', $text);
        }
        else if(preg_match('/^image\//', $mime_type)){
            // try OCR
            $text = utf8_encode((new TesseractOCR($path))->run());
        }
        else if(preg_match('/^text\//', $mime_type)){
            $text = file_get_contents($path);
        }
        else{ // for doc, docx, ppt, pptx, xls, xlsx
                $doc = new \App\DocXtract($path);
                $text = $doc->convertToText();
        }
        return $text;
   }

   public function setCollectionId($collection_id){
   	$this->collection_id = $collection_id;
   }

   public function canSave($url){
	   $desired_list = DesiredUrl::where('collection_id', $this->collection_id)->get();
	   if(!empty($desired_list)){
		   foreach($desired_list as $d_u){
		   	$pattern = '#'.$d_u->url_start_pattern.'#';
		   	$subject_url = (string) $url;
		   	if(preg_match($pattern, $subject_url)) return true;
			else return false;
		   }
	   }

	   $suppression_list = UrlSuppression::where('collection_id', $this->collection_id)->get();
	   if(!empty($suppression_list)){
	   foreach($suppression_list as $s){
		   $pattern = '#'.$s->url_start_pattern.'#';
		   $subject_url = (string) $url;
		   if(preg_match($pattern, $subject_url)) return false;
	   }
	   }
	   return true;
   }
}
