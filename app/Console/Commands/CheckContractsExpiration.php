<?php

namespace App\Console\Commands;

use App\Models\Contrat;
use App\Notifications\ContractExpiringNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckContractsExpiration extends Command
{
    protected $signature = 'contrats:check-expiration';

    protected $description = "Envoie une alerte mail aux locataires dont le contrat arrive à échéance (30, 15 et 7 jours avant la fin)";

    protected array $thresholds = [30, 15, 7];

    public function handle(): int
    {
        foreach ($this->thresholds as $days) {
            $targetDate = Carbon::today()->addDays($days)->toDateString();

            $contrats = Contrat::where('status', 'actif')
                ->whereDate('end_date', $targetDate)
                ->with(['tenant', 'appartement'])
                ->get();

            foreach ($contrats as $contrat) {
                if ($contrat->tenant) {
                    $contrat->tenant->notify(new ContractExpiringNotification($contrat, $days));
                    $this->info("Alerte envoyée à {$contrat->tenant->email} (contrat #{$contrat->id}, J-{$days})");
                }
            }
        }

        return self::SUCCESS;
    }
}
