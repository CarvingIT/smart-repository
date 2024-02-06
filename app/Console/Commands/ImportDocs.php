<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Elastic\Elasticsearch\ClientBuilder;
use App\MetaField;
use App\Taxonomy;

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
        $elastic_hosts = env('ELASTIC_SEARCH_HOSTS', 'localhost:9200');
        $hosts = explode(",",$elastic_hosts);
        $client = ClientBuilder::create()->setHosts($hosts)
                ->setBasicAuthentication('elastic', env('ELASTIC_PASSWORD','some-default-password'))
                ->setCABundle('/etc/elasticsearch/certs/http_ca.crt')
                ->build();

		// Is elastic search running ?
		// get the indices and see if an error is returned
		$params = [ 'index' => 'sr_documents', 
					    'body'  => [
    					    'query' => [
            					'match' => [
                					'testField' => 'abc'
            					]
        					]
    					]
				];
		try{
			$es_on = true;
			$results = $client->search($params);
		}
		catch(\Elasticsearch\Common\Exceptions\NoNodesAvailableException $e){
			echo "WARNING: ElasticSearch nodes are not available. When the service starts, re-indexing will have to be done.\n";
			$es_on = false;
		}

        $collection_id = $this->argument('collection_id');
        $dir = $this->option('dir');
        $csv = $this->option('csv');
        //echo "$collection_id  $dir  $csv\n";
        if(empty($dir)){
            echo "Aborting. Option --dir must be specified.\n";
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
				foreach ($fields as $f){
					$f_model = MetaField::where('label',$f)
							->where('collection_id', $collection_id)
							->first();	
					if($f_model){
						$field_models[] = $f_model;
					}
					else{
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
								$val_ar = explode(',',$values[$i]);
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
				print_r($meta_values);
			}
			#exit;
            //list the directory and take each file path in array
            $list = scandir('storage/app/import');
            foreach($list as $f){
		// don't import meta.csv
		if ($f == 'meta.csv') continue;

                if(is_file('storage/app/import/'.$f)){
					print_r(@$meta_values[$f]);
                   	$d = \App\Http\Controllers\DocumentController::importFile($collection_id, 'import/'.$f, @$meta_values[$f]);
					// update title
					if(!empty($titles[$f])){
						$d->title = $titles[$f];
						$d->save();
					}
                   	echo $dir.'/'.$f."\n";
	    			// Update elastic index
					if($es_on){
                    try{
            	    	$body = $d->toArray();
		   		    	$body['collection_id'] = $collection_id;
            			$params = [
                			'index' => 'sr_documents',
                			'id'    => $d->id,
                			'body'  => $body
            			];

            			$response = $client->index($params);
            			print_r($response);
                    }
                    catch(\Exception $e){
                    	echo "!! Error while importing $f\n";
                    	echo $e->getMessage()."\n";
                    }
				  } // if ES is on
                }
            }
        }
    }
}
