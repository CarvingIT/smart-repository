<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MetaFieldValue;
use App\ReverseMetaFieldValue;

class ReindexMetaValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:ReindexMetaValues';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an (reverse) index of meta values';

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
		// empty reverse meta values table first
		ReverseMetaFieldValue::truncate();
		// add again
		$meta_values = MetaFieldValue::where('value', 'like','[%]')->get();//pagination needed
		foreach($meta_values as $mv){
			$values = json_decode($mv->value);
			foreach($values as $v){
				if(empty($v)) continue;
				$rmfv = new ReverseMetaFieldValue;
				$rmfv->document_id = $mv->document_id;
				$rmfv->meta_field_id = $mv->meta_field_id;
				$rmfv->meta_value = $v;
				$rmfv->save();
			}
		}
    }
}
