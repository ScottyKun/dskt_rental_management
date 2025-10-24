<?php
namespace App\Repositories;
use Illuminate\Support\Facades\Auth;

use App\Models\Appartement;
use App\Models\Immeuble;

class AppartementRepository 
{
    //creer un appartement
    public function create(array $data): Appartement
    {
        return Appartement::create($data);
    }

    //supprimer un appartement
    public function delete(int $id): bool
    {
        $appartement = Appartement::find($id);
        if (!$appartement) {
            return false;
        }

        return $appartement->delete();
    }

    //update un appartement
    public function update(int $id, array $data): bool
    {
        $appartement = Appartement::find($id);
        if (!$appartement) {
            return false;
        }

        return $appartement->update($data);
    }

    //lister les appartements
    public function all(int $page=10){
        return Appartement::with(['immeuble', 'locataire'])
            ->orderByDesc('created_at')
            ->paginate($page);
    }

    //rechercher par id
    public function findById(int $id): ?Appartement
    {
        return Appartement::with(['immeuble', 'locataire'])->find($id);
    }

    //rechercher critères
    public function search(?string $term = null, int $perPage = 7)
    {
        $query = Appartement::with(['immeuble', 'locataire']);

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                ->orWhere('description', 'LIKE', "%{$term}%")
                ->orWhere('type', 'LIKE', "%{$term}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    //trouver les appartements d'un immeuble
    public function findByImmeubleIds(array $immeubleIds, int $perPage = 7)
    {
        return Appartement::with(['immeuble', 'locataire'])
            ->whereIn('immeuble_id', $immeubleIds)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    //statistiques
    // Total appartements
    public function countAll(): int
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return Immeuble::sum('nb_apartments');
        }

        // Gestionnaire : seulement les appartements de ses immeubles
       $immeubleIds = Immeuble::where('manager_id', $user->id)->pluck('id');
        return Immeuble::whereIn('id', $immeubleIds)->sum('nb_apartments');
    }

    // Appartements disponibles
    public function countDisponibles(): int
    {
        $user = Auth::user();
        $query = Appartement::where('status', 'disponible');

        if ($user->role === 'gestionnaire') {
            $immeubleIds = Immeuble::where('manager_id', $user->id)->pluck('id');
            $query->whereIn('immeuble_id', $immeubleIds);
        }

        return $query->count();
    }

    // Appartements occupés
    public function countOccupes(): int
    {
        $user = Auth::user();
        $query = Appartement::where('status', 'occupe');

        if ($user->role === 'gestionnaire') {
            $immeubleIds = Immeuble::where('manager_id', $user->id)->pluck('id');
            $query->whereIn('immeuble_id', $immeubleIds);
        }

        return $query->count();
    }

    // Appartements en rénovation
    public function countEnRenovation(): int
    {
        $user = Auth::user();
        $query = Appartement::where('status', 'en_renovation');

        if ($user->role === 'gestionnaire') {
            $immeubleIds = Immeuble::where('manager_id', $user->id)->pluck('id');
            $query->whereIn('immeuble_id', $immeubleIds);
        }

        return $query->count();
    }
    
}