<?php

namespace App\Notifications;

use App\CollectionSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollectionSubscriptionPaymentCompleted extends Notification
{
    use Queueable;

    protected CollectionSubscription $subscription;

    public function __construct(CollectionSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $collection = $this->subscription->collection;
        $validTill = $this->subscription->till_date ? $this->subscription->till_date->format('d-m-Y') : 'N/A';

        return (new MailMessage)
            ->subject('Your subscription payment was received')
            ->greeting('Hello ' . ($notifiable->name ?: 'User') . ',')
            ->line('Your payment for ' . $collection->name . ' was recorded successfully.')
            ->line('Challan: ' . $this->subscription->challan)
            ->line('Amount: ' . number_format((float) $this->subscription->amount, 2))
            ->line('Access is valid until: ' . $validTill)
            ->action('Open Collection', url('/collection/' . $collection->id))
            ->line('If you did not expect this message, please contact your administrator.');
    }
}