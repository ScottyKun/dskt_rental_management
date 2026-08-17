<?php

namespace App\Services;

use App\Models\User;
use App\Models\Immeuble;
use App\Models\Appartement;
use App\Models\Contrat;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * KPI globaux pour le dashboard admin.
     */
    public function adminOverview(): array
    {
        return [
            'occupation' => [
                'total_appartements' => Appartement::count(),
                'disponibles' => Appartement::where('status', 'disponible')->count(),
                'occupes' => Appartement::where('status', 'occupe')->count(),
                'taux_occupation' => $this->tauxOccupation(),
                'vacants_longue_duree' => $this->appartementsVacantsLongueDuree(),
            ],
            'finance' => [
                'revenus_du_mois' => $this->revenusConfirmes(null, Carbon::now()),
                'revenu_total' => $this->revenuTotal(null),
                'revenus_mois_dernier' => $this->revenusConfirmes(null, Carbon::now()->subMonthNoOverflow()),
                'montant_impaye' => $this->montantImpaye(),
                'nb_loyers_en_retard' => $this->nbLoyersEnRetard(),
                'taux_recouvrement' => $this->tauxRecouvrement(),
                'cautions' => $this->cautionOverview(null),
            ],
            'contrats' => [
                'actifs' => Contrat::where('status', 'actif')->count(),
                'total' => Contrat::count(),
                'expirant_30j' => $this->contratsExpirantSous(30),
                'expirant_60j' => $this->contratsExpirantSous(60),
                'expirant_90j' => $this->contratsExpirantSous(90),
                'duree_moyenne_jours' => $this->dureeMoyenneContrats(),
            ],
            'utilisateurs' => [
                'locataires_actifs' => User::where('role', 'locataire')->where('is_validated', true)->count(),
                'locataires_en_attente' => User::where('role', 'locataire')->where('is_validated', false)->count(),
                'nouveaux_locataires_30j' => User::where('role', 'locataire')
                    ->where('created_at', '>=', Carbon::now()->subDays(30))->count(),
                'gestionnaires' => User::where('role', 'gestionnaire')->count(),
            ],
            'documents' => [
                'cni_validees' => Contrat::where('document_status', 'valide')->count(),
                'cni_en_attente' => Contrat::whereIn('document_status', ['demande', 'soumis'])->count(),
                'cni_en_attente_longue' => Contrat::whereIn('document_status', ['demande', 'soumis'])
                    ->where('document_requested_at', '<=', Carbon::now()->subDays(5))
                    ->count(),
            ],
            'messages' => [
                'non_traites' => \App\Models\Message::where('is_read', false)->count(),
            ],
        ];
    }

    /**
     * Repartition du parc par gestionnaire ("attribution des proprietaires").
     */
    public function ownerAttribution(): array
    {
        return User::where('role', 'gestionnaire')
            ->get()
            ->map(function (User $manager) {
                $appartementsQuery = Appartement::whereHas('immeuble', fn($q) => $q->where('manager_id', $manager->id));

                return [
                    'id' => $manager->id,
                    'nom' => trim($manager->name . ' ' . $manager->surname),
                    'immeubles' => Immeuble::where('manager_id', $manager->id)->count(),
                    'appartements_occupes' => (clone $appartementsQuery)->where('status', 'occupe')->count(),
                    'appartements_total' => (clone $appartementsQuery)->count(),
                    'revenus_du_mois' => $this->revenusConfirmes($manager->id, Carbon::now()),
                ];
            })
            ->toArray();
    }

    /**
     * Immeubles/appartements sans gestionnaire assigne (donnee orpheline).
     */
    public function ressourcesSansGestionnaire(): array
    {
        return [
            'immeubles' => Immeuble::whereNull('manager_id')->count(),
        ];
    }

    /**
     * KPI scopes pour un gestionnaire donne.
     */
    public function managerOverview(int $managerId): array
    {
        $appartementsQuery = Appartement::whereHas('immeuble', fn($q) => $q->where('manager_id', $managerId));
        $contratsQuery = Contrat::whereHas('appartement.immeuble', fn($q) => $q->where('manager_id', $managerId));

        return [
            'immeubles' => Immeuble::where('manager_id', $managerId)->count(),
            'appartements_disponibles' => (clone $appartementsQuery)->where('status', 'disponible')->count(),
            'appartements_occupes' => (clone $appartementsQuery)->where('status', 'occupe')->count(),
            'taux_occupation' => $this->tauxOccupation($managerId),
            'contrats_actifs' => (clone $contratsQuery)->where('status', 'actif')->count(),
            'locataires_actifs' => (clone $contratsQuery)->where('status', 'actif')
                ->distinct('tenant_id')->count('tenant_id'),
            'contrats_expirant_30j' => $this->contratsExpirantSous(30, $managerId),
            'revenus_du_mois' => $this->revenusConfirmes($managerId, Carbon::now()),
            'revenu_total' => $this->revenuTotal($managerId),
            'cautions' => $this->cautionOverview($managerId),
            'montant_impaye' => $this->montantImpaye($managerId),
            'nb_loyers_en_retard' => $this->nbLoyersEnRetard($managerId),
            'cni_en_attente' => (clone $contratsQuery)->whereIn('document_status', ['demande', 'soumis'])->count(),
        ];
    }

    /**
     * Mini-dashboard personnel pour un locataire.
     */
    public function tenantOverview(int $tenantId): array
    {
        $contrat = Contrat::where('tenant_id', $tenantId)->where('status', 'actif')->latest()->first();

        return [
            'contrat' => $contrat,
            'jours_restants_contrat' => $contrat ? (int) round(Carbon::now()->diffInDays($contrat->end_date, false)) : null,
            'loyer_a_jour' => $contrat ? $this->loyerAJour($contrat) : null,
            'jour_paiement' => $contrat->rent_payment_day ?? null,
            'document_status' => $contrat->document_status ?? null,
            'signature_status' => $contrat->signature_status ?? null,
        ];
    }

    /**
     * Le loyer est "a jour" si :
     * - il a ete paye ce mois-ci, OU
     * - on n'a pas encore atteint le jour d'echeance du mois en cours (pas encore du).
     * Il est "en retard" uniquement si le jour d'echeance est passe sans paiement confirme ce mois-ci.
     */
    private function loyerAJour(Contrat $contrat): bool
    {
        $now = Carbon::now();

        $payeCeMois = Payment::where('tenant_id', $contrat->tenant_id)
            ->confirmed()
            ->whereMonth('paid_at', $now->month)
            ->whereYear('paid_at', $now->year)
            ->where('motif', 'loyer')
            ->exists();

        if ($payeCeMois) {
            return true;
        }

        // Pas encore paye ce mois : a jour tant que l'echeance n'est pas encore passee.
        return $now->day < $contrat->rent_payment_day;
    }

    // ------------------------------------------------------------------
    // Datasets pour Chart.js
    // ------------------------------------------------------------------

    /**
     * Evolution des revenus confirmes sur les N derniers mois.
     */
    public function revenueTrend(?int $managerId = null, int $months = 6): array
    {
        $labels = [];
        $data = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonthsNoOverflow($i);
            $labels[] = ucfirst($date->translatedFormat('M Y'));
            $data[] = (float) $this->revenusConfirmes($managerId, $date);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function occupancyDonut(?int $managerId = null): array
    {
        $query = $managerId
            ? Appartement::whereHas('immeuble', fn($q) => $q->where('manager_id', $managerId))
            : Appartement::query();

        return [
            'labels' => ['Occupés', 'Disponibles', 'En rénovation'],
            'data' => [
                (clone $query)->where('status', 'occupe')->count(),
                (clone $query)->where('status', 'disponible')->count(),
                (clone $query)->where('status', 'en_renovation')->count(),
            ],
        ];
    }

    public function contractsStatusDonut(?int $managerId = null): array
    {
        $query = $managerId
            ? Contrat::whereHas('appartement.immeuble', fn($q) => $q->where('manager_id', $managerId))
            : Contrat::query();

        return [
            'labels' => ['Actifs', 'Résiliés'],
            'data' => [
                (clone $query)->where('status', 'actif')->count(),
                (clone $query)->where('status', 'résilié')->count(),
            ],
        ];
    }

    public function revenueByManagerChart(): array
    {
        $attribution = $this->ownerAttribution();

        return [
            'labels' => array_column($attribution, 'nom'),
            'data' => array_column($attribution, 'revenus_du_mois'),
        ];
    }

    // ------------------------------------------------------------------
    // Helpers internes
    // ------------------------------------------------------------------

    private function tauxOccupation(?int $managerId = null): float
    {
        $query = $managerId
            ? Appartement::whereHas('immeuble', fn($q) => $q->where('manager_id', $managerId))
            : Appartement::query();

        $total = (clone $query)->count();
        if ($total === 0) {
            return 0.0;
        }

        $occupes = (clone $query)->where('status', 'occupe')->count();

        return round(($occupes / $total) * 100, 1);
    }

    private function revenusConfirmes(?int $managerId, Carbon $forMonth): float
    {
        $query = Payment::confirmed()
            ->whereMonth('paid_at', $forMonth->month)
            ->whereYear('paid_at', $forMonth->year);

        if ($managerId) {
            $query->where('manager_id', $managerId);
        }

        return (float) $query->sum('amount');
    }

    private function revenuTotal(?int $managerId = null): float
    {
        $query = Payment::confirmed();

        if ($managerId) {
            $query->where('manager_id', $managerId);
        }

        return (float) $query->sum('amount');
    }

    /**
     * Vue synthétique des dépôts de garantie des contrats actifs.
     * Les paiements de caution sont rapprochés du locataire puisque Payment
     * ne possède pas encore de contract_id.
     */
    private function cautionOverview(?int $managerId = null): array
    {
        $query = Contrat::where('status', 'actif')
            ->whereNotNull('deposit_amount')
            ->where('deposit_amount', '>', 0)
            ->with(['tenant', 'appartement']);

        if ($managerId) {
            $query->whereHas('appartement.immeuble', fn($q) => $q->where('manager_id', $managerId));
        }

        $contracts = $query->get();
        $totalPrevu = 0.0;
        $totalPaye = 0.0;
        $totalRestant = 0.0;
        $aPayer = [];

        foreach ($contracts as $contrat) {
            $prevu = (float) $contrat->deposit_amount;
            $paye = (float) Payment::where('tenant_id', $contrat->tenant_id)
                ->confirmed()
                ->where('motif', 'caution')
                ->sum('amount');

            // On ne peut pas imputer un paiement de caution à un contrat
            // précis tant que Payment ne possède pas de contract_id.
            // On limite donc le montant imputé au dépôt du contrat courant.
            $payeImpute = min($paye, $prevu);
            $restant = max(0, $prevu - $payeImpute);

            $totalPrevu += $prevu;
            $totalPaye += $payeImpute;
            $totalRestant += $restant;

            if ($restant > 0) {
                $aPayer[] = [
                    'nom' => $contrat->tenant
                        ? trim($contrat->tenant->name . ' ' . $contrat->tenant->surname)
                        : '—',
                    'appartement' => $contrat->appartement->name ?? '—',
                    'montant' => $prevu,
                    'paye' => $payeImpute,
                    'reste' => $restant,
                    'date_limite' => $contrat->deposit_due_date?->format('d/m/Y'),
                    'en_retard' => $contrat->deposit_due_date
                        ? Carbon::today()->gt($contrat->deposit_due_date)
                        : false,
                ];
            }
        }

        return [
            'montant_prevu' => $totalPrevu,
            'montant_paye' => $totalPaye,
            'montant_restant' => $totalRestant,
            'nb_a_payer' => count($aPayer),
            'a_payer' => $aPayer,
        ];
    }

    private function montantImpaye(?int $managerId = null): float
    {
        // Somme des loyers dus (contrats actifs) non couverts par un paiement confirme ce mois-ci.
        $query = Contrat::where('status', 'actif');
        if ($managerId) {
            $query->whereHas('appartement.immeuble', fn($q) => $q->where('manager_id', $managerId));
        }

        $now = Carbon::now();
        $total = 0.0;

        $query->with('tenant')->chunk(100, function ($contrats) use (&$total, $now) {
            foreach ($contrats as $contrat) {
                $paye = Payment::where('tenant_id', $contrat->tenant_id)
                    ->confirmed()
                    ->whereMonth('paid_at', $now->month)
                    ->whereYear('paid_at', $now->year)
                    ->where('motif', 'loyer')
                    ->exists();

                if (!$paye && $now->day >= $contrat->rent_payment_day) {
                    $total += (float) $contrat->rent_amount;
                }
            }
        });

        return $total;
    }

    private function nbLoyersEnRetard(?int $managerId = null): int
    {
        $query = Contrat::where('status', 'actif');
        if ($managerId) {
            $query->whereHas('appartement.immeuble', fn($q) => $q->where('manager_id', $managerId));
        }

        $now = Carbon::now();
        $count = 0;

        $query->chunk(100, function ($contrats) use (&$count, $now) {
            foreach ($contrats as $contrat) {
                $paye = Payment::where('tenant_id', $contrat->tenant_id)
                    ->confirmed()
                    ->whereMonth('paid_at', $now->month)
                    ->whereYear('paid_at', $now->year)
                    ->where('motif', 'loyer')
                    ->exists();

                if (!$paye && $now->day >= $contrat->rent_payment_day) {
                    $count++;
                }
            }
        });

        return $count;
    }

    /**
     * Liste nominative des locataires actifs, separee en "a jour" et "en retard/a venir",
     * pour affichage direct sur le dashboard (KPI demande : qui a paye / qui n'a pas paye).
     */
    public function rentPaymentStatusList(?int $managerId = null): array
    {
        $query = Contrat::where('status', 'actif')->with('tenant', 'appartement');
        if ($managerId) {
            $query->whereHas('appartement.immeuble', fn($q) => $q->where('manager_id', $managerId));
        }

        $now = Carbon::now();
        $paid = [];
        $unpaid = [];

        $query->get()->each(function (Contrat $contrat) use (&$paid, &$unpaid, $now) {
            if (!$contrat->tenant) {
                return;
            }

            $paye = Payment::where('tenant_id', $contrat->tenant_id)
                ->confirmed()
                ->whereMonth('paid_at', $now->month)
                ->whereYear('paid_at', $now->year)
                ->where('motif', 'loyer')
                ->exists();

            $entry = [
                'nom' => trim($contrat->tenant->name . ' ' . $contrat->tenant->surname),
                'appartement' => $contrat->appartement->name ?? '—',
            ];

            if ($paye) {
                $paid[] = $entry;
            } else {
                $unpaid[] = $entry;
            }
        });

        return ['paid' => $paid, 'unpaid' => $unpaid];
    }

    private function tauxRecouvrement(): float
    {
        $attendu = Contrat::where('status', 'actif')->sum('rent_amount');
        if ($attendu <= 0) {
            return 0.0;
        }

        $query = Payment::confirmed()
            ->where('motif', 'loyer')
            ->whereMonth('paid_at', Carbon::now()->month)
            ->whereYear('paid_at', Carbon::now()->year);

        $encaisse = (float) $query->sum('amount');

        return round(($encaisse / $attendu) * 100, 1);
    }

    private function contratsExpirantSous(int $days, ?int $managerId = null): int
    {
        $query = Contrat::where('status', 'actif')
            ->whereBetween('end_date', [Carbon::now(), Carbon::now()->addDays($days)]);

        if ($managerId) {
            $query->whereHas('appartement.immeuble', fn($q) => $q->where('manager_id', $managerId));
        }

        return $query->count();
    }

    private function dureeMoyenneContrats(): ?float
    {
        $contrats = Contrat::whereNotNull('start_date')->get(['start_date', 'end_date']);

        if ($contrats->isEmpty()) {
            return null;
        }

        $totalJours = $contrats->sum(function (Contrat $contrat) {
            $fin = $contrat->end_date ?? Carbon::now();
            return Carbon::parse($contrat->start_date)->diffInDays(Carbon::parse($fin));
        });

        return round($totalJours / $contrats->count());
    }

    private function appartementsVacantsLongueDuree(int $days = 30): int
    {
        return Appartement::where('status', 'disponible')
            ->where('updated_at', '<=', Carbon::now()->subDays($days))
            ->count();
    }
}
