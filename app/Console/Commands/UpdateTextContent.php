<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Document;
use App\Collection;
use App\Util;
use Illuminate\Support\Facades\Log;

class UpdateTextContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:UpdateTextContent {collection_id : ID of the collection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracts associated text from documents and updates if different from the earlier version.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $collection_id = $this->argument('collection_id');
        if($collection_id == 'all'){
            $collections = Collection::all();
            foreach($collections as $c){
                $this->updateTextContentOfCollection($c);
            }
        }
        else{
            $c = Collection::find($collection_id);
            $this->updateTextContentOfCollection($c);
        }
        
        return Command::SUCCESS;
    }
    
    private function updateTextContentOfCollection($c){
        if($c->content_type != 'Uploaded documents'){
            echo $c->name." is not of type 'Uploaded documents'. Ignoring.\n";
            return false;
        }
        
        if($c->storage_drive != 'local'){
            echo $c->name." uses a storage drive that is not supported at this point in time. Ignoring.\n";
            return false;
        }
        
        $documents = Document::where('collection_id', $c->id)->chunk(100, function($documents){ 
            foreach($documents as $document){
                $current_text_content = $document->text_content;
                try{
                    $extracted_text_content = Util::extractText(storage_path('app/'.$document->path)); 
                    if($extracted_text_content != $current_text_content){
                        $document->text_content = $extracted_text_content;
                        $document->save();
                        echo "Updated document record ".$document->id."\n"; 
                    }
                }
                catch(\Exception $e){
                    echo $e->getMessage()."\n";
                }
            }
        });
        
        $this->call('ES:RebuildElasticIndex', ['collection_id'=> $c->id]);
        echo "Completed collection - ". $c->name."\n";
        return true;
    }
}
