<?php

namespace App\Notifications;

use App\Models\Contrat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RentPaymentDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param Contrat $contrat
     * @param int $daysLate 0 = échéance du jour, >0 = nombre de jours de retard
     */
    public function __construct(protected Contrat $contrat, protected int $daysLate = 0)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->greeting('Bonjour ' . $notifiable->name . ',');

        if ($this->daysLate > 0) {
            $mail->subject('Loyer en retard — DSKT Rental')
                ->line("Votre loyer de {$this->contrat->rent_amount} pour l'appartement « {$this->contrat->appartement?->name} » présente un retard de {$this->daysLate} jour(s).")
                ->line('Merci de régulariser votre situation dès que possible.');
        } else {
            $mail->subject('Rappel — Loyer à échéance aujourd’hui')
                ->line("Votre loyer de {$this->contrat->rent_amount} pour l'appartement « {$this->contrat->appartement?->name} » est dû aujourd'hui.");
        }

        return $mail->action('Voir mes paiements', route('payments.index'));
    }
}
