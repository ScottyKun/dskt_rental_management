<?php

namespace App\Notifications;

use App\Models\Contrat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ContratDocumentSubmittedNotification extends Notification implements ShouldQueue
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
            ->subject('Document transmis — validation requise')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Le locataire a transmis une pièce pour le contrat n°' . $this->contrat->numero . '.')
            ->action('Vérifier le document', route('contrats.documents'))
            ->line('Le contrat ne pourra être envoyé pour signature qu’après votre validation de ce document.');
    }
}