<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Elastic\Elasticsearch\ClientBuilder;
use App\MetaField;
use App\Taxonomy;
use App\Http\Controllers\DocumentController;

class ImportDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:ImportDocs {collection_id : ID of the collection} 
                {dir : Full path of the directory containing the documents to be imported}
                {--no-dry-run} {--show-meta-data}';

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
        $dry_run = ($this->option('no-dry-run')) ? false : true;
        $show_meta_data = ($this->option('show-meta-data')) ? true : false;

        if($dry_run) {echo "This is a dry run.\n";}
        else{ echo "Not a dry run. Importing documents.\n"; }

        $collection_id = $this->argument('collection_id');
        $dir = $this->argument('dir');
        if(empty($dir)){
            echo "Aborting. Argument {dir} must be specified.\n";
        }
        if($dir){
	    // create a sym link storage/app/import pointing to this dir
	    @unlink('storage/app/import');
	    symlink($dir, 'storage/app/import');
			//meta info file exists ?
			$meta_info_file = 'storage/app/import/meta.csv';
			$meta_values = [];
			$titles = [];
			if(is_file($meta_info_file)){
				$meta_lines = file($meta_info_file);
				$header_row = array_shift($meta_lines);
				// explode by \t char (tab separated values)	
				$fields = explode("\t", $header_row);
				$field_models = [];
                $field_num = 0;
				foreach ($fields as $f){
                    $field_num++;
                    // first column must be the filename.
                    // field-model cannot be found for the values in there.
                    if($field_num === 1){
                        $field_models[] = null;
                        continue; 
                    }
					$f_model = MetaField::where('label',$f)
							->where('collection_id', $collection_id)
							->first();	
					if($f_model){
						$field_models[] = $f_model;
					}
					else{
                        echo "Cannot find a meta field for label - ". $f." in the header row.\n";
						$field_models[] = null;
					}
				}
				foreach($meta_lines as $l){
					$values = explode("\t", $l);
					$row = [];
					for($i=0; $i<count($fields); $i++){
						$key = !empty($field_models[$i]) ? $field_models[$i]->id : $fields[$i];
						if($key == 'title'){
							$titles[$values[0]] = $values[$i];
							continue;
						}
						if($field_models[$i]){
							if($field_models[$i]->type == 'TaxonomyTree'){
								$t_id = $field_models[$i]->options;
								$t = Taxonomy::find($t_id);
								if(!$t) continue;
								$t_family = $t->createFamily(); 
								$val_ar = explode('|',$values[$i]);
								$t_ids = [];
								foreach($t_family as $tfm){
									foreach($val_ar as $v){
										if($tfm->label == $v){
											//echo "ID of $v - ".$tfm->id."\n";
											$t_ids[] = $tfm->id;
										}
									}
								}
								$row = ['field_id' => $key, 'field_value' => array_unique($t_ids)];
								$meta_values[$values[0]][] = $row;
							}
							else{ // text, textarea etc are all default
								$row = ['field_id' => $key, 'field_value'=>$values[$i]];	
								$meta_values[$values[0]][] = $row;
							}
						}
					}
				}
			}
			#exit;
            //list the directory and take each file path in array
            $list = scandir('storage/app/import');
            foreach($list as $f){
    	     // don't import meta.csv
		     if ($f == 'meta.csv') continue;
             if(is_file('storage/app/import/'.$f)){
                if($dry_run){
                    if($show_meta_data){
               	        echo $dir.'/'.$f."\n";
                        print_r(@$titles[$values[0]]);
				        print_r(@$meta_values[$f]);
                    }
                }
                if(!$dry_run){
               	    $d = DocumentController::importFile($collection_id, 'import/'.$f, @$meta_values[$f]);
				    // update title
				    if(!empty($titles[$f])){
					    $d->title = $titles[$f];
					    $d->save();
				    }
                }
             }
           }
        }
    }
}
