<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MfaCodeNotification extends Notification
{
    use Queueable;

    /**
     * Volontairement PAS de ShouldQueue : le code doit partir immédiatement,
     * l'utilisateur attend dessus pour se connecter.
     */
    public function __construct(protected string $code, protected int $validForMinutes)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre code de connexion — DSKT Rental')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Voici votre code de vérification pour finaliser votre connexion :')
            ->line("## {$this->code}")
            ->line("Ce code expire dans {$this->validForMinutes} minutes.")
            ->line("Si vous n'êtes pas à l'origine de cette tentative de connexion, ignorez cet e-mail et pensez à changer votre mot de passe.");
    }
}
