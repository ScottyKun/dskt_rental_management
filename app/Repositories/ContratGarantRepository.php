<?php

namespace App\Repositories;

use App\Models\ContratGarant;

class ContratGarantRepository
{
    public function createOrUpdate(int $contratId, array $data): ContratGarant
    {
        return ContratGarant::updateOrCreate(
            ['contrat_id' => $contratId],
            $data
        );
    }

    public function findByContrat(int $contratId): ?ContratGarant
    {
        return ContratGarant::where('contrat_id', $contratId)->first();
    }
}
