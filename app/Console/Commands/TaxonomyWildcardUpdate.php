<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Taxonomy;
use App\MetaField;
use App\MetaFieldValue;

class TaxonomyWildcardUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:TaxonomyWildcardUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wildcards values like "All" are handled. Meta field values and Reverse Meta field values are created.';

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
		$models_type_all = Taxonomy::where('label','all')->get();	
		// Get meta fields of type TaxonomyTree
		$taxonomy_fields = MetaField::where('type', 'TaxonomyTree')->get();
		$family = [];
		$family_ids = [];
		foreach($models_type_all as $m){
			// get all models in the tree of the parent
			$family[$m->id] = $m->parent->createFamily();
			echo $m->id . " - ".count($family[$m->id])."\n";
			foreach($family[$m->id] as $t){
				$family_ids[$m->id][] = ''.$t->id;
			}
		}
		print_r($family_ids);
		//print_r($family_ids);
		foreach($taxonomy_fields as $mf){
			// get meta_values for this field
			foreach(array_keys($family) as $all_val_id){
				$meta_value = MetaFieldValue::where('meta_field_id', $mf->id)
				->where('value','regexp','[^\d]'.$all_val_id.'[^\d]')
				->first();
				if(!$meta_value) continue;
				//print_r(json_decode($meta_value->value));	
				// new value
				//echo 'New value: '. json_encode($family_ids[$all_val_id]);
				$meta_value->value = json_encode($family_ids[$all_val_id]);
				$meta_value->save();
			}
		}
    }
}
