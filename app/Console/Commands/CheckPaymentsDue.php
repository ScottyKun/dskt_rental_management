<?php

namespace App\Console\Commands;

use App\Models\Contrat;
use App\Models\Payment;
use App\Notifications\RentPaymentDueNotification;
use App\Services\MessageService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckPaymentsDue extends Command
{
    protected $signature = 'payments:check-due';

    protected $description = "Envoie une alerte mail le jour de l'échéance du loyer puis un rappel de retard tous les 5 jours";

    public function __construct(protected MessageService $messageService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $today = Carbon::today();

        $contrats = Contrat::where('status', 'actif')
            ->with(['tenant', 'appartement'])
            ->get();

        foreach ($contrats as $contrat) {
            if (!$contrat->tenant) {
                continue;
            }

            $dueDay = min($contrat->rent_payment_day, $today->daysInMonth);
            $dueDate = Carbon::create($today->year, $today->month, $dueDay);

            if ($today->lt($dueDate)) {
                continue; // pas encore échu ce mois-ci
            }

            $alreadyPaid = Payment::confirmed()
                ->where('tenant_id', $contrat->tenant_id)
                ->whereYear('paid_at', $today->year)
                ->whereMonth('paid_at', $today->month)
                ->exists();

            if ($alreadyPaid) {
                continue;
            }

            $daysLate = $today->diffInDays($dueDate);

            // Alerte le jour J, puis tous les 5 jours de retard (5, 10, 15...)
            if ($daysLate === 0 || $daysLate % 5 === 0) {
                $contrat->tenant->notify(new RentPaymentDueNotification($contrat, $daysLate));

                $this->messageService->notifyInApp(
                    $contrat->tenant->id,
                    null,
                    $daysLate > 0 ? 'Loyer en retard' : 'Loyer à échéance aujourd\'hui',
                    $daysLate > 0
                        ? "Votre loyer présente un retard de {$daysLate} jour(s)."
                        : "Votre loyer est dû aujourd'hui."
                );

                $this->info("Rappel envoyé à {$contrat->tenant->email} (contrat #{$contrat->id}, retard {$daysLate}j)");
            }
        }

        return self::SUCCESS;
    }
}