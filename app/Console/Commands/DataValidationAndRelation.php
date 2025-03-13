<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DataValidationAndRelation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DataValidation:DataRelation {data_csv_file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The data validation of the CSV file will be done in this command. The parent-child relationship will bbe set here.';

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
        //
	$file = $this->argument('data_csv_file');
        $handle = fopen($file, "r");
	$csv_columns = [];
	$fp_data_discrip = fopen("RE_data_discripancies.txt","w");
	$fp_notin_db = fopen("RE_data_absent_in_database.csv","w");
	$fp_notin_sheet = fopen("RE_Repos_doc_shortname.csv","w");
	$heading_set = 0;

        while (($row = fgetcsv($handle)) !== FALSE) {
                // do something with row values
		if(in_array('Document title',$row) && $heading_set == 0){
			foreach($row as $column){
				$col = strtolower($column);
				$col = preg_replace("/\s+|\//","-",$col);
				$csv_columns[] = $col;
			}
			//$title_row = implode(",",$row);
			fwrite($fp_notin_db,$row[13]."\n");
			fwrite($fp_notin_sheet,$row[13]."\n");
			$heading_set = 1;
			//print_r($csv_columns); 
		}
		$document = \App\Document::select('id','collection_id','path','type','size')
					->where('title',$row[11])->first();
		
		if(!empty($document)){
		//echo $document->id.", ".$document->collection_id.", ".$document->path.", ".$document->type.", ".$document->size."\n"; 
			// Check csv file values with database values
			foreach($document->collection->meta_fields as $meta_field){
				if(is_null($document->meta_value($meta_field->id, $row))){
				//write a text file with the data discripancies.
					$data = ['Doc ID: '.$document->id, $meta_field->label];
					$data_discrip = implode(",",$data);
					fwrite($fp_data_discrip,$data_discrip."\n");
				}
			}
			//print_r($display_meta);
		}	
		else{
		//the record present in the datasheet but not in the database.
			//$data_csv = implode(",",$row);
			fwrite($fp_notin_db,$row[13]."\n"); //List of Document Short Name, absent in database 
		}
		//exit;
		$db_document = \App\Document::select('id','collection_id','path','type','size')
				->get();
		foreach($db_document as $db_doc){
			if($db_doc->meta_value(4) != $row[13]){
			//the record present in the db but not in the RE datasheet.
				fwrite($fp_notin_sheet,$db_doc->meta_value(4)."\n"); //Document Short Name absent in sheet 
			}
		}
        }
	fclose($fp_data_discrip);
	fclose($fp_notin_db);
	fclose($fp_notin_sheet);
        fclose($handle);
    }
}
