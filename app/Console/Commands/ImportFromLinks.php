<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ImportLink;
use Illuminate\Support\Facades\Storage;

class ImportFromLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:ImportFromLinks';
	protected $client;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the queue of document links and imports them to respective collection.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
		$this->client = new \GuzzleHttp\Client([ 'cookies' => true, 'verify' => false ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		echo "Starting import\n";
		$links = ImportLink::all();
		foreach($links as $l){
			$download_link = '';
			$params = array();
			if(preg_match('#([^.]*).google.com/#', $l->url, $subdomain)){
				// get file ID
				preg_match('#/d/([^\/]*)/#', $l->url, $matches);
				if(empty($matches[1])){
					preg_match('#id=([^&]*)#', $l->url, $matches);
				}
				try{
					$path = $this->googleDrivePublicFileDownload($subdomain[1],$matches[1]);	
					// import file
					$f = \App\Http\Controllers\DocumentController::importFile($l->collection_id, $path, json_decode($l->metadata));
				}
				catch(\Exception $e){
					echo "Can not import. ". $e->getMessage()."\n";
					exit;
					//continue;
				}
				$l->delete();
			}
			else{
				echo $l->collection_id . " ". $l->url."\n";
				// do nothing for now
			}
		}

    }

	public function googleDrivePublicFileDownload($subdomain, $file_id, $confirm=null){
		$client = $this->client;
		$download_link = $subdomain.'.google.com/uc?export=download&id='.$file_id;
		if(!empty($confirm)){
			$download_link = $subdomain.'.google.com/u/0/uc?export=download&confirm='.$confirm.'&id='.$file_id;
		}
		echo $download_link."\n";
		$response = $client->request('GET', $download_link, ['sink' => storage_path('app').'/links/'.$file_id]);
		$headers = $response->getHeaders();
		if(preg_match('#text/html;#', $headers['Content-Type'][0])){
			$html = $response->getBody();
			if(preg_match('#&amp;confirm=([^&]*)&amp;#', $html, $matches)){
				//echo $matches[1];
				return $this->googleDrivePublicFileDownload($subdomain, $file_id, $matches[1]);
			}
		}
		/*
		print_r($headers);
		exit;
		*/
		$content_disposition = $headers['Content-Disposition'][0];
		$parts = explode(";", $content_disposition);
		//print_r($parts);
		$filename = $parts[1];
		$filename = preg_replace('/filename="/','', $filename);
		$filename = preg_replace('/"/', '',$filename);
		$filename = preg_replace('/ /', '_',$filename);
		// rename the file
		rename(storage_path('app').'/links/'.$file_id, storage_path('app').'/links/'.$filename);
		return 'links/'.$filename;
	}
}
