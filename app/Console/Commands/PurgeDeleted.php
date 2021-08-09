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
			// The default value is 30 
			$purge_after_days = empty(env('PURGE_AFTER_DAYS'))?30:env('PURGE_AFTER_DAYS');
			if(strtotime($d->deleted_at) + $purge_after_days*24*60*60 < time()){
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
