<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ApprovalSaved as ApprovalSavedNotification;
use App\Document;

class ApprovalSaved
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
        // for now, send only if approvable is App\Document
        $approvable = $event->approval->approvable;
        if($approvable instanceof Document){
            $notifiable = $event->approval->approver_role;

		    try{
		    	Notification::send($notifiable, new ApprovalSavedNotification($event->approval));
		    }
		    catch(\Exception $e){
		    	Log::error($e->getMessage());
		    }
        }
        else{
		   	Log::debug(get_class($approvable));
        }
    }
}
