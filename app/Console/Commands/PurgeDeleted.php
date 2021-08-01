<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Document;
use Illuminate\Support\Facades\Storage;

class PurgeDeleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:PurgeDeleted';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes orphan files from storages';

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
		$trashed_docs = Document::onlyTrashed()->get();
		foreach($trashed_docs as $d){
			echo "Deleting ". $d->id." - ". $d->title." from storage - ".$d->collection->storage_drive."\n";
			// Purging policy - number of days after which orphan/soft-deleted files will get deleted
			// The value is a static value 30 which needs to be updated after policy configuration is in place.
			if(strtotime($d->deleted_at) + 30*24*60*60 < time()){
				try{
					Storage::disk($d->collection->storage_drive)->delete($d->path);
				}
				catch(\Exception $e){
					echo $e->getMessage()."\n";
				}
				// remove revisions
				$d->revisions->each(function ($r) {$r->forceDelete();
				});
				// remove the model
				$d->forceDelete();
			}
			else{
				echo "Not deleted because of purging policy.\n";
			}
		}
    }
}
