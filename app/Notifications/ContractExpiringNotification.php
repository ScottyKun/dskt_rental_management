<?php

namespace App\Notifications;

use App\Models\Contrat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ContractExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Contrat $contrat, protected int $daysLeft)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appartement = $this->contrat->appartement;

        return (new MailMessage)
            ->subject("Votre contrat de location arrive à échéance dans {$this->daysLeft} jour(s)")
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line("Votre contrat de location pour l'appartement « {$appartement?->name} » se termine le {$this->contrat->end_date->format('d/m/Y')}.")
            ->line('Pensez à contacter votre gestionnaire si vous souhaitez le renouveler.')
            ->action('Voir mon contrat', route('contrats.consult', $this->contrat->id));
    }
}
