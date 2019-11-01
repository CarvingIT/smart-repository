<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:ImportDocs {collection_id : ID of the collection} 
                {--dir= : Full path of the directory containing the documents to be imported} 
                {--csv= : Full path of the CSV file containing file paths and meta data }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import files into a collection that is already created.';

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
        $collection_id = $this->argument('collection_id');
        $dir = $this->option('dir');
        $csv = $this->option('csv');
        echo "$collection_id  $dir  $csv\n";
    }
}
