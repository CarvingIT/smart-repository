<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Collection;
use App\Document;
use Rap2hpoutre\FastExcel\FastExcel;

class ExportCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:ExportCollection {collection_id : ID of the collection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports a collection';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $collection_id = $this->argument('collection_id');
        $c = Collection::find($collection_id);
        $date_time = date('Y-m-d-H:i:s');
        $user_home = getenv('HOME');
        $filename = "$user_home/collection-$collection_id-$date_time.xlsx";
        echo "Exporting collection ".$c->name." to $filename.\n";

        $documents = Document::where('collection_id', $collection_id);
        $meta_fields = $c->meta_fields;
        $new_list  = $new_meta_details = [];
   
        $documents->chunk(100, function($documents) use (&$new_list){ // chunking starts
            foreach($documents as $d){
            $list = ['ID'=>$d->id,'Title'=>$d->title,'Path'=>$d->path];
            $meta_fields = $d->collection->meta_fields;
            $meta_details=[];
            foreach($meta_fields as $m){
                if($m->type=='MultiSelect' || $m->type == 'Select'){
                    $details_select = trim($d->meta_value($m->id),'[]');
                    $details_select = preg_replace('/"/',"",$details_select);
                    //$meta_details = [$m->label => $d->meta_value($m->id)];
                    $meta_details = [$m->label => $details_select];
                }
                else{
                    $extra_attributes = json_decode($m->extra_attributes);
                    $show_parents = empty($extra_attributes->show_parents)?false:true;
                    if($m->type == 'TaxonomyTree' && $show_parents){
                        $meta_details = [$m->label => html_entity_decode($d->meta_value($m->id, false, $show_parents))];
                    }
                    else{
                        $meta_details = [$m->label => html_entity_decode($d->meta_value($m->id))];
                    }
                }
                $list = array_merge($list,$meta_details);
                $related_doc_ids = $d->related_documents->pluck('related_document_id');
                $list['Related document IDs'] = implode("|", $related_doc_ids->all());
        }
            $new_list[] = array_merge($list,$meta_details);
        }
        });// chunking ends

        (new FastExcel($new_list))->export($filename);        
        return Command::SUCCESS;
    }
}
