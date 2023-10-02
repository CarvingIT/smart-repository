<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Approval;
use App\Collection;

class BinshopsPostSaved
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
		if($event->binshops_post->approvals->count() == 0){
			// get the first role id from approval workflow
			$collection = Collection::find(1);
			$col_conf = json_decode($collection->column_config);
			$approvers = $col_conf->approved_by;
			$approval_record = new Approval(['approved_by_role'=>$approvers[0]]);
			$event->binshops_post->approvals()->save($approval_record);	
		}
    }
}
