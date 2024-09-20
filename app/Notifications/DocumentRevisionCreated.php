<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Messages\SlackMessage;

class DocumentRevisionCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($document_revision)
    {
		$this->document_revision = $document_revision;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        //return ['mail'];
        return ['mail', 'slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('A new revision of document - "'. $this->document_revision->document->title.'" has been created.')
                    ->action('View', url('/collection/'.$this->document->collection->id.'/document/'.$this->document->id.'/details'));
                    //->line(env('APP_NAME'). ' Team');
    }

   public function toSlack($notifiable){
        Log::debug('Sending document-revision notification by slack');
            $content = 'Smart Repository: A new revision of document - "'. $this->document_revision->document->title.'" has been created.';
            return (new SlackMessage)
            ->error()
            ->content($content);
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
