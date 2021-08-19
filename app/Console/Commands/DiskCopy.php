<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DiskCopy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:DiskCopy {source_disk : Source Disk} {destination_disk : Destination Disk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copies all files from one disk to another';

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
		$source_disk = $this->argument('source_disk');
		$destination_disk = $this->argument('destination_disk');

		echo "Copying files from disk - $source_disk to disk - $destination_disk.\n";
		$files = Storage::disk($source_disk)->allFiles('smartarchive_assets');
		$dest_files = Storage::disk($destination_disk)->allFiles('smartarchive_assets');
	
		foreach ($files as $file) {
			try{
				if(in_array($file, $dest_files)){
					echo "Skipping $file \n";
					continue;
				}
				echo "Copying $file\n";
				Storage::disk($destination_disk)->put($file, Storage::disk($source_disk)->get($file));
				echo "Copied ".$file."\n";
			}
			catch(\Exception $e){
				echo "ERROR: ".$e->getMessage()."\n";
			}
		}
    }
}
