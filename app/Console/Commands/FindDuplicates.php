<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Duplicate;
use App\Document;

class FindDuplicates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:FindDuplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds duplicate documents and stores the results in the database.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // truncate the db table of duplicates 
        Duplicate::truncate();
        // rehash the documents table
        Document::chunk(100, function($documents){
            foreach($documents as $d){
                if($d->text_content){// only create hashes if the content is not null nor empty
                    $d->hash = hash('sha256', $d->text_content);
                    $d->save();
                }
            }
        });
        // compare hashes and update the duplicates 
        $document_count = Document::count();
        echo $document_count." documents in all.\n";
        $i=0;
        $duplicates = [];
        $ignore_docs = [];
        while($i < $document_count){
            $d_ori = Document::orderBy('id')->skip($i)->take(1)->first();
            $i++;
            if(in_array($d_ori->id, $ignore_docs)) continue; 
            Document::chunk(10, function($documents) use($d_ori, &$duplicates,&$ignore_docs){
                foreach($documents as $d){
                    if($d->id == $d_ori->id) continue;
                    if(!empty($d->hash) && !empty($d_ori->hash) && $d->hash == $d_ori->hash){
                        $duplicates[$d_ori->id][] = $d->id;
                        $ignore_docs[] = $d->id;
                    }
                }
            });
        }
        print_r($duplicates);
        foreach($duplicates as $k=>$v){
            $duplicate = new Duplicate;
            $duplicate->document_id = $k;
            $duplicate->duplicates = json_encode($v);
            $duplicate->save();
        }
        return Command::SUCCESS;
    }
}
