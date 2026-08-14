<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected DashboardService $dashboardService
    ) {
    }

    public function index()
    {
        $user = $this->authService->currentuser();

        return match ($user->role) {
            'admin' => view('dashboards.admin', [
                'user' => $user,
                'kpis' => $this->dashboardService->adminOverview(),
                'attribution' => $this->dashboardService->ownerAttribution(),
                'ressourcesOrphelines' => $this->dashboardService->ressourcesSansGestionnaire(),
                'revenueTrend' => $this->dashboardService->revenueTrend(),
                'occupancyDonut' => $this->dashboardService->occupancyDonut(),
                'contractsDonut' => $this->dashboardService->contractsStatusDonut(),
                'revenueByManager' => $this->dashboardService->revenueByManagerChart(),
                'rentStatus' => $this->dashboardService->rentPaymentStatusList(),
            ]),
            'gestionnaire' => view('dashboards.gestionnaire', [
                'user' => $user,
                'kpis' => $this->dashboardService->managerOverview($user->id),
                'revenueTrend' => $this->dashboardService->revenueTrend($user->id),
                'occupancyDonut' => $this->dashboardService->occupancyDonut($user->id),
                'contractsDonut' => $this->dashboardService->contractsStatusDonut($user->id),
                'rentStatus' => $this->dashboardService->rentPaymentStatusList($user->id),
            ]),
            // Le locataire n'a pas de page d'accueil generique : son "accueil" est directement son logement,
            // qui embarque son propre mini-dashboard (voir AppartementController::locataire).
            'locataire' => redirect()->route('tenant.logement'),
            default => abort(403, 'Unauthorized action.'),
        };
    }
}
