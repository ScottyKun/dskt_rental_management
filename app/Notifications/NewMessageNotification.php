<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Message $message)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('DSKT Rental — ' . $this->message->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($this->message->content)
            ->action('Voir le message', route('messages.consult', $this->message->id))
            ->line('Vous recevez cet e-mail car une notification a été générée sur votre compte DSKT Rental.');
    }
}
