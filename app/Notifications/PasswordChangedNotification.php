<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre mot de passe a été modifié')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Le mot de passe de votre compte DSKT Rental vient d\'être modifié avec succès.')
            ->line('Si vous êtes à l\'origine de cette modification, aucune action n\'est nécessaire.')
            ->line('Si vous n\'êtes pas à l\'origine de ce changement, contactez immédiatement votre gestionnaire.');
    }
}