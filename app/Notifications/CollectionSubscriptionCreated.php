<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollectionSubscriptionCreated extends Notification
{
    use Queueable;

    protected $resetUrl;
    protected $loginUrl;

    public function __construct($resetUrl, $loginUrl)
    {
        $this->resetUrl = $resetUrl;
        $this->loginUrl = $loginUrl;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your account has been created')
            ->greeting('Hello ' . ($notifiable->name ?: 'User') . ',')
            ->line('An administrator has created your account and subscribed you to one or more collections.')
            ->line('To get started, please reset your password using the button below.')
            ->action('Reset Password', $this->resetUrl)
            ->line('After resetting your password, login using the URL below:')
            ->line($this->loginUrl)
            ->line('If you did not expect this email, please contact your administrator.');
    }
}
