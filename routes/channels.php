<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Contrat;

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('contrat.{contratId}', function ($user, $contratId) {
    $contrat = Contrat::with('appartement.immeuble')->find($contratId);

    if (!$contrat) {
        return false;
    }

    // Admin : accès à tous les contrats
    if ($user->role === 'admin') {
        return true;
    }

    // Locataire : uniquement son propre contrat
    if ($user->role === 'locataire') {
        return (int) $user->id === (int) $contrat->tenant_id;
    }

    // Gestionnaire : uniquement les contrats des immeubles qu'il gère
    if ($user->role === 'gestionnaire') {
        return (
            $contrat->appartement?->immeuble?->manager_id &&
            (int) $contrat->appartement->immeuble->manager_id === (int) $user->id
        );
    }

    return false;
});