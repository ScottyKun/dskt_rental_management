<?php

namespace App\Notifications;

use App\Models\Contrat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ContratDocumentRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Contrat $contrat)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Document requis pour votre contrat de location')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line("Votre gestionnaire a besoin d'une pièce d'identité (CNI) pour finaliser votre contrat de location.")
            ->action('Transmettre le document', route('contrats.consult', $this->contrat->id))
            ->line('Le contrat ne pourra être signé qu’après validation de ce document.');
    }
}
