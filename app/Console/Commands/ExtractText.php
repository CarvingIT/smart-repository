<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Document;
use App\Util;

class ExtractText extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:ExtractText {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attempts to extract and index text from documents.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $extent = $this->option('all') ? 'all' : 'blank';
        if($extent == 'blank'){
            echo "Attempting to extract text from documents where there's no extracted text.\n";
            $documents = Document::whereNull('text_content')->orWhere('text_content','')->orderBy('id','desc');
        }
        else{
            echo "Attempting to extract text from all documents all over again.\n";
            $documents = Document::whereRaw('1 = 1')->orderBy('id','desc');
        }
        // chunk
        $documents->chunk(100, function($docs){
            foreach($docs as $d){
                echo $d->id."\n";
                $text_content = '';
                $path = is_array(json_decode($d->path)) ? json_decode($d->path) : [$d->path];
                foreach($path as $p){
                    echo $p."\n";
                    try{
                        $text_content .= Util::extractText(storage_path('app/'.$p));
                    }
                    catch(\Exception $e){
                        echo $e->getMessage();
                    }
                }
                $d->text_content = $text_content;
                $d->save();
            }
        });
        return Command::SUCCESS;
    }
}
