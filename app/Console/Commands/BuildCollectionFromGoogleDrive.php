<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Collection;
use App\Document;
use App\Disk;

use Illuminate\Support\Facades\Storage;
use Google\Service\Drive;

use Spatie\PdfToText\Pdf;
use mishagp\OCRmyPDF\OCRmyPDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


class BuildCollectionFromGoogleDrive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:BuildCollectionFromGoogleDrive {collection_id : ID of the collection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Documents are already present on Google Drive. This command builds collection from those documents.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $collections_array = [];
        if(preg_match('/all/i',$this->argument('collection_id'))){
           echo "All\n"; 
           $collections = Collection::get();
            foreach($collections as $c){
                $collections_array[] = $c;
                /*
                echo $c->name." = ".$c->storage_drive."\n"; 
                $disk = \App\Disk::where('name',$c->storage_drive)->first();
                if(!empty($disk->config)){
                    $disk_config =  json_decode($disk->config,true);
                    echo $disk_config['driver']."\n";
                }
                else continue;
                */
            } 
        }
        else{
            $collection_id = $this->argument('collection_id');
            $collection = \App\Collection::find($collection_id);
            $collections_array[] = $collection;
        }
/*
    print_r($collections_array);
    echo "\n";
    foreach($collections_array as $collection){
    echo $collection->id."=".$collection->name."\n";
        $disk = \App\Disk::where('name',$collection->storage_drive)->first();
        if(!empty($disk->config)){
            $disk_config =  json_decode($disk->config,true);
            $driver = $disk_config['driver'];
            echo "Disk Drive - ".$disk_config['driver']."\n";
            if($disk_config['driver'] != 'google'){
                continue;
            }
        }
        else continue;
    }
exit;
*/

        foreach($collections_array as $collection){//This command will run for each collection id in the collections table.
            $collection_id = $collection->id;

        // Below is the Working code 
        /* 
        //OLD Code
        $collection_id = $this->argument('collection_id');
        $collection = \App\Collection::find($collection_id);
        $storage_drive = empty($collection->storage_drive) ? 'local' : $collection->storage_drive;
        $driver = config("filesystems.disks.{$storage_drive}.driver");
        if($driver != 'google'){ 
            echo "Sorry, this command is only for Google Drive.\n";
            exit;
        }
        */
        $driver = '';
        $disk = \App\Disk::where('name',$collection->storage_drive)->first();
        if(!empty($disk->config)){
            $disk_config =  json_decode($disk->config,true);
            //echo $disk_config['driver']."\n";
            $driver = $disk_config['driver'];
            if($disk_config['driver'] != 'google'){
                continue;
            }
        }
        else continue;
        $storage_drive = empty($collection->storage_drive) ? 'local' : $collection->storage_drive;

        echo "Collection disk type: ".$driver."\n";
        echo "Collection disk name: ".$storage_drive."\n";
        echo "\n";

        echo "Started importing the documents: \n";
        echo "\n";

        //$disk = \App\Disk::where('name','=',$collection->storage_drive)->first();
        $config = $disk_config = json_decode($disk->config);
        $folderName = $disk_config->folderId;

        /* Get the $folderId from $folderName */
        $adapter = Storage::disk($storage_drive)->getAdapter();
        // Get the underlying Google_Service_Drive instance
        $service = $adapter->getService();

        $parameters = [
            'pageSize' => 1, // Only need one result
            'fields' => 'files(id, name)', // Request only the 'id' and 'name' fields
            'q' => "name = '{$folderName}' and mimeType = 'application/vnd.google-apps.folder'"
        ];

        $results = $service->files->listFiles($parameters);
        $files = $results->getFiles();
        //print_r($files); exit;
        if(!empty($files)){
            $folderId = $files[0]->getId();
            //echo $folderId; exit;
        }
        else{
            echo "Folder not found\n";
            exit;
        }


        /* Get the files using the actual $folderId */
        //$filesWithMetadata = Storage::disk($storage_drive)->allFiles('1Xfjckwnut4gyqejVlLErog_gknBj10SW',true);
        $filesWithMetadata = Storage::disk($storage_drive)->allFiles($folderId);

        //$maxFileSize = 10*1024*1024; //10,485,760 bytes
        $maxFileSize = 10485760; //10,485,760 bytes
        foreach($filesWithMetadata as $file){
            echo $file."\n";

            //check if the file exists
                if (Storage::disk($storage_drive)->exists($file)) {
                echo "File exists on drive.\n";
                    // File exists
                }
                else{ echo "File not found on the drive.\n"; continue;}

            $meta = Storage::disk($storage_drive)->getAdapter()->getMetadata($file);
            //print_r($meta);
            //echo $file."\n";
//exit;
                if($meta['fileSize'] >= $maxFileSize){
                echo "- Can not import. The file size is greater than 10MB. File Size - ".$meta['fileSize']."\n\n";
                continue;
                }     

                $filepath = $meta['path'];
                $fileId = $meta['extraMetadata']['id'];
                $file_name = $meta['extraMetadata']['name'];
                $file_ext = $meta['extraMetadata']['extension'];
                $file_virtual_path = $meta['extraMetadata']['virtual_path'];
                $file_display_path = $meta['extraMetadata']['display_path'];
                $file_title = $meta['extraMetadata']['filename'];
                $mimetype = $meta['mimeType']; 
                $new_filename = '1_' . time() . '_' . $file_name;

                /* Save document */
                $d = \App\Document::where('path',$filepath)->first();
                if(!empty($d->id)){
                echo "File details exist in database\n";
                echo "\n";
                continue;
                }
                //else{
                $d = new Document();
                $d->title = $file_title;
                $d->collection_id = $collection_id;
                $d->created_by = 1;
                $d->size = $meta['fileSize'];
                $d->type = $meta['mimeType'];
                //$d->path = $filepath;
                $d->path = $meta['path'];
                $d->ori_filename = $file_name;
                $text_content = '';

                // Saved locally for text extraction
                if(!preg_match('/application\/vnd.google-apps.*/',$meta['mimeType'])){
                    $rawData = Storage::disk($storage_drive)->get($meta['path']);
                }
                else{
                    if($meta['mimeType'] == 'application/vnd.google-apps.document'){
                    $expected_mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';//for .docx
                    }
                    else{
                    $expected_mimeType = 'application/pdf'; 
                    }
                    try{
                    $response = $service->files->export($fileId, $expected_mimeType, ['alt' => 'media']);
                    $rawData = $content = $response->getBody()->getContents();
                    }
                    catch(\Exception $e){
                        echo $e->getMessage();
                        echo "\nCould not import. Need to import manually\n";
                        continue;
                    }    
                }
                if(empty($rawData)){ echo "File is empty.".$file_name."\n"; continue;}

                Storage::disk('local')->put('smartarchive_assets/'.$collection_id.'/'.'1'.'/'.$new_filename , $rawData);
                $local_filepath = storage_path("app/smartarchive_assets/".$collection_id."/"."1"."/".$new_filename);

                try{
                    $text_content = \App\Util::extractText($local_filepath);
                    // Delete the file if the storage drive is other than local drive.
                    if ($storage_drive != 'local') {
                       Storage::disk('local')->delete('smartarchive_assets/'.$collection_id.'/'.'1'.'/'.$new_filename);
                    }
                }
                catch (\Exception $e) {
                    //Log::error($e->getMessage());
                    $d->text_content = '';
                    $warnings[] = 'No text was indexed. Text extraction and indexing will be attempted later.';
                }
                $d->text_content = mb_convert_encoding($text_content, "UTF-8");
                $d->save();
                echo "File details imported \n";
                echo "\n";
                //}
        }

        }//foreach ends for all collections

        echo "Finished importing the documents. \n";
        
        return Command::SUCCESS;
    }

}
