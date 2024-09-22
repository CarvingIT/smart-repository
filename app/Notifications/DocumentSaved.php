<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Messages\SlackMessage;

class DocumentSaved extends Notification
{
    use Queueable;
	protected $document;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($document)
    {
		$this->document = $document;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // return two channels currently available in SR code
        return ['mail','slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
		Log::debug('Sending notification by email');
        if($this->document->wasRecentlyCreated){
        return (new MailMessage)
                    ->subject(env('APP_NAME','Smart Repository'). ': New document')
                    ->line('Document - "'. $this->document->title.'" has been added to collection - "'.$this->document->collection->name.'"')
                    ->action('View', url('/collection/'.$this->document->collection->id.'/document/'.$this->document->id.'/details'));
                    //->line(env('APP_NAME','Smart Repository').' Team');
        }
        else{
        return (new MailMessage)
                    ->subject(env('APP_NAME','Smart Repository'). ': Document updated')
                    ->line('Document - "'. $this->document->title.'" from collection "'.$this->document->collection->name.'" has been updated.')
                    ->action('View', url('/collection/'.$this->document->collection->id.'/document/'.$this->document->id.'/details'));
                    //->line(env('APP_NAME','Smart Repository').' Team');
        }
    }

	public function toSlack($notifiable){
		Log::debug('Sending notification by slack');
		if($this->document->wasRecentlyCreated){
			return (new SlackMessage)
			->error()
			->content('Smart Repository: A new document "'. $this->document->title.'" has been added.');
		}
		else{
			$content = 'Smart Repository: Document - "'. $this->document->title.'" has been updated.';
			return (new SlackMessage)
			->error()
			->content($content);
		}
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
