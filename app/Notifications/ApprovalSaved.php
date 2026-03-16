<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use App\Approval;
use App\Document;

class ApprovalSaved extends Notification
{
    use Queueable;

    protected $approval;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Approval $approval)
    {
        $this->approval = $approval;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
		Log::debug('Sending awaiting-approval notification by email');
        $approvable = $this->approval->approvable;
        if ($approvable instanceof Document) {
            return (new MailMessage)
                ->subject(env('APP_NAME','Smart Repository'). ': Awaiting approval')
                ->line('A document "'.$approvable->title.'" is awaiting your approval.')
                ->action('Check', url('/document/'.$approvable->id.'/approval'));
        }
        return (new MailMessage)
            ->subject(env('APP_NAME','Smart Repository'). ': Awaiting approval')
            ->line('An item is awaiting your approval.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
