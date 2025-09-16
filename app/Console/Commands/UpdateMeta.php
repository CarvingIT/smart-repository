<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Document;
use App\Collection;
use App\MetaField;
use App\MetaFieldValue;
use App\Taxonomy;

class UpdateMeta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:UpdateMeta 
                {meta_data_file : Full path of the CSV file containing Document IDs and meta data }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk update of meta that uses exported data file as the base file';

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
        $meta_data_file = $this->argument('meta_data_file');
		echo $meta_data_file."\n";

		if(!file_exists($meta_data_file)){
			die("Error: Meta data file does not exist.\n");
		}

        $handle = fopen($meta_data_file, "r");
        $fields = fgetcsv($handle, null, "\t");

        while(($values = fgetcsv($handle, null, "\t")) !== FALSE){
			if(empty($values[0])) continue;
			$doc = Document::find($values[0]);
			if(!$doc){
			echo "Document ID ".$values[0]." was not found. Continuing ..\n";
			continue;
			}
			echo $doc->title."\n";
			for($i=1; $i<count($fields); $i++){
				$meta_field = MetaField::where('collection_id', $doc->collection->id)
					->where('label', ltrim(rtrim($fields[$i])))->first();
				if(!$meta_field){
					echo "Could not find meta field - ". $fields[$i]."\n";
					continue;
				}
				//echo $meta_field->id.": ".$values[$i]."\n";
				// check type of the meta field
				$field_val_model = MetaFieldValue::where('document_id', $doc->id)
					->where('meta_field_id', $meta_field->id)->first();
				if(!$field_val_model){
					$field_val_model = new MetaFieldValue;
					$field_val_model->document_id = $doc->id;
					$field_val_model->meta_field_id = $meta_field->id;
				}
				if($meta_field->type == 'TaxonomyTree'){
					// find relevant parent (category) in the taxonomies
					$taxo_parent = $meta_field->options;
					// there can be more than one values separated by pipes "|"
					$val_ar = explode("|", $values[$i]);
                    $val_ar = array_map('trim', $val_ar);

                    $t_category_model = Taxonomy::where('id', $taxo_parent)->first();
                    //echo 'Category: '. $t_category_model->label."\n";
                    $t_family = $t_category_model->createFamily();
                    $family_ids = [];
                    foreach($t_family as $t_f){
                        $family_ids[] = $t_f->id;
                    }

					$t_models = Taxonomy::whereIn('parent_id', $family_ids)
						->whereIn('label', $val_ar)->get();
					$t_ids = [];
					foreach($t_models as $t){
						$t_ids[] = $t->id;
					}
					$field_val_model->value = '['.implode(",", $t_ids).']';
                    //echo $field_val_model->value."\n";
				}
				else if($meta_field->type == "Select" || $meta_field->type == "MultiSelect"){
					$field_val_model->value = '['.$values[$i].']';
				}
				else{ // default handling for type = Text|TextArea|Date|Numeric 
					$field_val_model->value = $values[$i];
				}
				// save the meta value
				$field_val_model->save();
			}
		}
    }
}
