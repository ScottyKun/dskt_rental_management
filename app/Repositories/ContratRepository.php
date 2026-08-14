<?php
namespace App\Repositories;
use Illuminate\Support\Facades\Auth;

use App\Models\Contrat;

class ContratRepository
{
    //creer un contrat
    public function create(array $data)
    {
        $contrat = Contrat::create($data);

        // Numero genere apres coup a partir de l'id (garanti unique, sequentiel)
        $contrat->update([
            'numero' => 'CTR-' . $contrat->created_at->format('Y') . '-' . str_pad($contrat->id, 5, '0', STR_PAD_LEFT),
        ]);

        return $contrat;
    }

    public function countsAll(): array
    {
        return $this->countsFor(Contrat::query());
    }

    public function countsByManager(int $managerId): array
    {
        return $this->countsFor(
            Contrat::whereHas('appartement.immeuble', fn($q) => $q->where('manager_id', $managerId))
        );
    }

    public function countsByTenant(int $tenantId): array
    {
        return $this->countsFor(Contrat::where('tenant_id', $tenantId));
    }

    private function countsFor($query): array
    {
        return [
            'total' => (clone $query)->count(),
            'actifs' => (clone $query)->where('status', 'actif')->count(),
            'resilies' => (clone $query)->where('status', 'résilié')->count(),
            'expires' => (clone $query)->where('status', 'actif')
                ->where('end_date', '<', now())
                ->count(),
        ];
    }

    //modifier un contrat
    public function update(int $id, array $data): bool
    {
        $contrat = Contrat::find($id);
        if (!$contrat) {
            return false;
        }

        return $contrat->update($data);
    }

    //supprimer un contrat
    public function delete(int $id): bool
    {
        $contrat = Contrat::find($id);
        if (!$contrat) {
            return false;
        }

        return $contrat->delete();
    }

    //rechercher un contrat selon criteres
    public function search(?string $term = null, int $perPage = 7)
    {
        $query = Contrat::with(['tenant', 'appartement']);

        if ($term) {
            $query->where(function ($q) use ($term) {

                $q->where('status', 'LIKE', "%{$term}%")

                ->orWhereHas('tenant', function ($tenantQuery) use ($term) {
                    $tenantQuery->where('name', 'LIKE', "%{$term}%")
                                ->orWhere('surname', 'LIKE', "%{$term}%");
                })

                ->orWhereHas('appartement', function ($appQuery) use ($term) {
                    $appQuery->where('name', 'LIKE', "%{$term}%");
                });
            });
        }

        return $query->orderBy('start_date', 'desc')->paginate($perPage);
    }


    //rechercher par id
    public function findById(int $id): ?Contrat
    {
        return Contrat::with(['tenant', 'appartement'])->find($id);
    }
    //lister les contrats
    public function all(int $page=10){
        return Contrat::with(['tenant', 'appartement'])
            ->orderByDesc('start_date')
            ->paginate($page);
    }

    //lister selon locataire
    public function listByTenant(int $tenantId, int $page=10){
        return Contrat::with(['tenant', 'appartement'])
            ->where('tenant_id', $tenantId)
            ->orderByDesc('start_date')
            ->paginate($page);
    }

    //lister selon manager
   public function listByManager(int $managerId, int $page = 10){
        return Contrat::with(['tenant', 'appartement'])
            ->whereHas('appartement', function ($q) use ($managerId) {
                $q->whereHas('immeuble', function ($sub) use ($managerId) {
                    $sub->where('manager_id', $managerId);
                });
            })
            ->orderByDesc('start_date')
            ->paginate($page);
    }

    //pour le scheduler
    public function updateStatuses()
    {
        Contrat::where('end_date', '<', now())
            ->where('status', 'actif')
            ->update(['status' => 'expiré']);
    }

    // Contrats expirant dans 1 jour
    public function expiringTomorrow()
    {
        return Contrat::whereDate('end_date', '=', now()->addDay()->toDateString())
            ->where('status', 'actif')
            ->get();
    }

    // Contrats expirant aujourd'hui
    public function expiringToday()
    {
        return Contrat::whereDate('end_date', '=', today()->toDateString())
            ->where('status', 'actif')
            ->get();
    }

}