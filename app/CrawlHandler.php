<?php
namespace App;
use Spatie\Crawler\CrawlObserver;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use App\Url;

class CrawlHandler extends CrawlObserver{
   var $collection_id = null;

   public function willCrawl(UriInterface $url) {
	echo "Starting crawling $url\n";
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
			$url_model->size = strlen($content);
			try{
				$url_model->save();
			}
			catch(\Exception $e){
				echo "ERROR: ".$e->getMessage()."\n"; // log error to a log file instead of to the standard output
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
		return ''; // code needs to be handled for mime-types other than text/html
	}
   }

   private function getText($mime_type, $content){
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
        else if(preg_match('/^image\//', $d->type)){
            // try OCR
            $text = utf8_encode((new TesseractOCR($path))->run());
        }
        else if(preg_match('/^text\//', $d->type)){
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
}
