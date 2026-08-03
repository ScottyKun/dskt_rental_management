<?php

namespace App\Repositories;

use App\Models\ContratDocument;

class ContratDocumentRepository
{
    public function create(array $data): ContratDocument
    {
        return ContratDocument::create($data);
    }

    public function update(ContratDocument $document, array $data): bool
    {
        return $document->update($data);
    }

    public function findById(int $id): ?ContratDocument
    {
        return ContratDocument::with(['contrat.tenant', 'contrat.appartement'])->find($id);
    }

    /**
     * Toutes les pieces jointes (derniere en date par contrat), pour l'espace CNI admin/gestionnaire.
     * Optionnellement filtrees par recherche (nom du locataire) et par statut.
     */
    public function search(?string $term = null, ?string $status = null, ?int $managerId = null)
    {
        $query = ContratDocument::query()
            ->with(['contrat.tenant', 'contrat.appartement.immeuble'])
            ->whereIn('id', function ($sub) {
                // La derniere piece soumise par contrat uniquement
                $sub->selectRaw('MAX(id)')->from('contrat_documents')->groupBy('contrat_id');
            });

        if ($managerId) {
            $query->whereHas('contrat.appartement.immeuble', fn($q) => $q->where('manager_id', $managerId));
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($term) {
            $query->whereHas('contrat.tenant', function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('surname', 'like', "%{$term}%");
            });
        }

        return $query->orderByDesc('created_at')->paginate(10)->withQueryString();
    }
}
