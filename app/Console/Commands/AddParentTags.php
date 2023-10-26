<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MetaField;
use App\MetaFieldValue;
use App\Taxonomy;

class AddParentTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:AddParentTags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This applies to meta values of type Taxonomy. If a lower level of tags are selected, their parents get added by this command. This is needed for drill-down links where parent selection is necessary.';

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
		$taxonomies = Taxonomy::all();
		$t_parents = [];
		foreach($taxonomies as $t){
			$t_parents[$t->id] = $t->parent_id;
		}
		$meta_fields = MetaField::where('type', 'TaxonomyTree')->get();
		foreach($meta_fields as $m){
			$meta_values = MetaFieldValue::where('meta_field_id', $m->id)->get();
			foreach($meta_values as $mv){
				$m_val_ar = (array) json_decode($mv->value);
				$new_vals = [];
				foreach($m_val_ar as $v){
					if(empty($t_parents[$v]) || $m->options == $t_parents[$v]) continue;
					$new_vals[] = (string)$t_parents[$v];
				}
				$m_val_ar = array_unique(array_merge((array)$m_val_ar, $new_vals));
				$m_val_ar = array_map('strval', $m_val_ar);
				// update the meta_values
				echo json_encode($m_val_ar)."\n";
				$mv->value = json_encode($m_val_ar);
				$mv->save();
			}
		}		

    }
}
