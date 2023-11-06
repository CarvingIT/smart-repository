<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Synonyms;

class ImportSynonyms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:ImportSynonyms {file : TSV file of synonyms}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports synonyms from a tab separated file.';

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
        $file = $this->argument('file');
		$synonyms = file($file);
		foreach($synonyms as $s){
			$s = ltrim(rtrim($s));
			if(empty($s)) continue;
			$s_ar = array_filter(explode("\t", $s));
			$new_synonyms = new Synonyms;
			$new_synonyms->synonyms = implode(',', $s_ar);
			$new_synonyms->save();
		}
    }
}
