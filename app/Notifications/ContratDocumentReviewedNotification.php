<?php

namespace App\Notifications;

use App\Models\ContratDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ContratDocumentReviewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected ContratDocument $document, protected bool $approved)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)->greeting('Bonjour ' . $notifiable->name . ',');

        if ($this->approved) {
            $mail->subject('Document validé — votre contrat peut être signé')
                ->line('Votre pièce jointe a été validée par votre gestionnaire.')
                ->line('Votre contrat est maintenant prêt à être signé électroniquement.');
        } else {
            $mail->subject('Document refusé — action requise')
                ->line('Votre pièce jointe a été refusée par votre gestionnaire.')
                ->line('Motif : ' . $this->document->rejection_reason)
                ->line('Merci de transmettre un nouveau document.');
        }

        return $mail->action('Voir mon contrat', route('contrats.consult', $this->document->contrat_id));
    }
}
