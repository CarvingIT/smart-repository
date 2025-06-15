<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Document;
use App\Url;

class MergeDocsInWebResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:MergeDocsInWebResources  {web_resources_collection_id : ID of the collection}
		{collections : Comma separated list of collection IDs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merges document collections in web resources without scrolling.';

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
        $web_resources_id = $this->argument('web_resources_collection_id');
        $collections = $this->argument('collections');
		$collection_ar = explode(",", $collections);
		
		echo "Merging collections - $collections into Web Resources Collection - $web_resources_id \n";
		$documents = Document::whereIn('collection_id',$collection_ar)->get();
		foreach($documents as $d){
			$doc_url = env('APP_URL').'/collection/'.$d->collection_id.'/document/'.$d->id."\n";
			//echo $url;
			$url = Url::where('url',$doc_url);
			if($url->count() == 0){
				echo "New url $doc_url \n";
				$url_model = new Url;
			}	
			else{
				echo "Updating $doc_url \n";
				$url_model = $url->first();
			}

			$url_model->url = $doc_url;
			$url_model->collection_id = $web_resources_id;
			$url_model->title = $d->title;
			$url_model->size = $d->size;
			$url_model->type = $d->type;
			$url_model->text_content = $d->text_content;
			
			$url_model->save();
		}
    }
}
