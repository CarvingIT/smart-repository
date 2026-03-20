<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ApprovalSaved as ApprovalSavedNotification;
use App\Document;
use App\Services\UserAlertService;

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

            if (is_null($event->approval->approval_status) && !empty($event->approval->approved_by_role)) {
                try {
                    $alertService = new UserAlertService();
                    $alertTitle = 'Approval required';
                    $alertMessage = 'Document "'.$approvable->title.'" is awaiting your approval.';
                    $alertUrl = '/document/'.$approvable->id.'/approval';
                    $alertService->createForRole(
                        (int) $event->approval->approved_by_role,
                        $alertTitle,
                        $alertMessage,
                        $alertUrl,
                        [
                            'type' => 'approval',
                            'approvable_type' => 'document',
                            'approvable_id' => $approvable->id,
                        ]
                    );
                }
                catch(\Exception $e){
                    Log::warning('Unable to create approval alert: '.$e->getMessage());
                }
            }
        }
        else{
		   	Log::debug(get_class($approvable));
        }
    }
}
