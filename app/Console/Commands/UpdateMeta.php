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

        $handle1 = fopen($meta_data_file, "r");
        $fields1 = fgetcsv($handle1, null, "\t");

############ Meta validation code starts here
        echo "\nValidating the data....\n";
            $validation_error_log = [];
        while(($values = fgetcsv($handle1, null, "\t")) !== FALSE){
			if(empty($values[0])) continue;
			$doc = Document::find($values[0]);
			if(!$doc){
            $validation_error_log[] = "Document ID ".$values[0]." was not found. Continuing ..";
			echo "Document ID ".$values[0]." was not found. Continuing ..\n";
			continue;
			}
			echo $doc->title."\n";
			for($i=1; $i<count($fields1); $i++){
                // Following fields have been skipped for validation
                if(preg_match('/Title/i', $fields1[$i]) || preg_match('/Path/i', $fields1[$i]) || preg_match('/Related document IDs/i',$fields1[$i])){ continue; }

				$meta_field = MetaField::where('collection_id', $doc->collection->id)
					->where('label', ltrim(rtrim($fields1[$i])))->first();
				if(!$meta_field){
                    $validation_error_log[] = "Could not find meta field - ". $fields1[$i];
					echo "Could not find meta field - ". $fields1[$i]."\n";
					continue;
				}
				//echo $meta_field->id.": ".$values[$i]."\n";
				// check type of the meta field
                    if($meta_field->type == 'Date'){
                                $date = $values[$i];
                                
                                if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
                                    $date_details = explode("-",$date);
                                    $d_y = trim($date_details[0]);
                                    $d_m = trim($date_details[1]);
                                    $d_d = trim($date_details[2]);
                                    //var_dump(checkdate($d_m, $d_d, $d_y));
                                    if(!checkdate($d_m, $d_d, $d_y)){
                                    $validation_error_log[] = "ERROR: For the field ".$meta_field->label." the value is not valid. ".$date;
                                    echo "ERROR: For the field ".$meta_field->label." the value is not valid. ".$date."\n";
                                    }
                                }
                                else{
                                    $validation_error_log[] = "ERROR: For the field ".$meta_field->label." the value is not in valid format. YYYY-MM-DD ".$date;
                                    echo "ERROR: For the field ".$meta_field->label." the value is not in valid format. YYYY-MM-DD ".$date."\n";
                                }
                    }

                    if($meta_field->type == 'Numeric'){
                                if (!filter_var($values[$i], FILTER_VALIDATE_INT)) {
                                    $validation_error_log[] = "ERROR: For the field ".$meta_field->label." the value '".$values[$i]."' is not numeric.";
                                    echo "ERROR: For the field ".$meta_field->label." the value '".$values[$i]."' is not numeric.\n";
                                }
                    }

                    if($meta_field->type == 'Select' || $meta_field->type == 'MultiSelect'){
                                $select_options = $meta_field->options;
                                if(empty($select_options)){
                                    $validation_error_log[] = "ERROR: This ".$meta_field->options." are not present in the portal.";
                                    echo "ERROR: This ".$meta_field->options." are not present in the portal.\n";
                                }
                                else{
                                    $select_options = explode(",",$select_options);
                                    $trimmed_select_options = array_map('trim',$select_options);

                                    if(!in_array($values[$i],$trimmed_select_options)){
                                        $validation_error_log[] = "ERROR: This '".$values[$i]."' value is not present in the options list of ".$meta_field->label." in the portal.";
                                        echo "ERROR: This '".$values[$i]."' value is not present in the options list of ".$meta_field->label." in the portal.\n";
                                    }
                                }
                    }


				if($meta_field->type == 'TaxonomyTree'){
					// find relevant parent (category) in the taxonomies
					$t_parent = $meta_field->options;
                    $t = Taxonomy::find($t_parent);
                    if(!$t){
                       $validation_error_log[]= "ERROR: ".$t->label." this taxonomy is not present in the portal.";
                       echo "ERROR: ".$t->label." this taxonomy is not present in the portal.\n";
                    }
				}
			}
		}

         if(!empty($validation_error_log)){
                    exit;
         }

################### Meta validation code ends here

####################################### Original code starts here
        echo "\nUpdating the data....\n";
        while(($values = fgetcsv($handle, null, "\t")) !== FALSE){
			if(empty($values[0])) continue;
			$doc = Document::find($values[0]);
			if(!$doc){
			echo "Document ID ".$values[0]." was not found. Continuing ..\n";
			continue;
			}
			echo $doc->id." ".$doc->title."\n";
			for($i=1; $i<count($fields); $i++){

                // Following fields have been skipped as they are not Meta Data Fields
                if(preg_match('/Path/i', $fields1[$i]) || preg_match('/Related document IDs/i',$fields1[$i])){ continue; }

                // Document title update
                if(preg_match('/Title/i',$fields1[$i]) && $doc->title != $values[$i]){
                    $doc->title = $values[$i];
                    $doc->save();
                    echo "New title - ".$doc->title."\n";
					continue;
                }
                elseif(preg_match('/Title/i',$fields1[$i]) && $doc->title == $values[$i]){
                    continue;
                }

                // Updating meta data
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
                    $duplicates_present = (count($t_models) > count($val_ar));
					$t_ids = [];
                    $t_ids_strict = [];
                    $label_cnt = [];
					foreach($t_models as $t){
						$t_ids[] = $t->id;
                        $label_cnt[$t->label] = empty($label_cnt[$t->label])?1:++$label_cnt[$t->label];
					}
					foreach($t_models as $t){
                        if(in_array($t->parent->label, $val_ar) || $t->parent->id == $taxo_parent){
                            $t_ids_strict[] = ''.$t->id; 
                        }
                    } 
					$field_val_model->value = json_encode($t_ids_strict);
                    //echo $field_val_model->value."\n";
				}
				else if($meta_field->type == "Select" || $meta_field->type == "MultiSelect"){
					$val_ar = explode("|", $values[$i]);
					$field_val_model->value = json_encode($val_ar);
                    //$field_val_model->value = '['.$values[$i].']';
				}
				else{ // default handling for type = Text|TextArea|Date|Numeric 
					$field_val_model->value = $values[$i];
				}
				// save the meta value
                //continue;
				$field_val_model->save();
            }
		}
    }
}
