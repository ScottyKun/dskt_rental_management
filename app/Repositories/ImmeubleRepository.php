<?php
namespace App\Repositories;

use App\Models\Immeuble;
use Illuminate\Support\Facades\Auth;

class ImmeubleRepository{


    //creer un immeuble
    public function create(array $data): Immeuble
    {
        return Immeuble::create($data);
    }

    //supprimer un immeuble
    public function delete(int $id): bool
    {
        $immeuble = Immeuble::find($id);
        if (!$immeuble) {
            return false;
        }

        return $immeuble->delete();
    }

    //update un immeuble
    public function update(int $id, array $data): bool
    {
        $immeuble = Immeuble::find($id);
        if (!$immeuble) {
            return false;
        }

        return $immeuble->update($data);
    }

    //lister les immeubles
    public function all(int $page=10){
        return Immeuble::orderBy('created_at', 'desc')->paginate($page);
    }

    //lister selon gestionnaire
    public function allByManager(int $managerId, int $page=7){
        return Immeuble::where('manager_id', $managerId)->orderBy('created_at', 'desc')->paginate($page);
    }

    //rechercher par id
    public function findById(int $id): ?Immeuble
    {
        return Immeuble::find($id);
    }

    //rechercher
     public function search(?string $term = null, int $perPage = 7)
    {
        $query = Immeuble::query();

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('town', 'LIKE', "%{$term}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    //afficher les immeubles selon le role pour nos selects
    public function findByGestionnaire(int $gestionnaireId)
    {
        return Immeuble::where('manager_id', $gestionnaireId)
            ->orderBy('name', 'asc')
            ->get();
    }

    //pour notre selecteur
    public function findAll()
    {
        return Immeuble::orderBy('name', 'asc')->get();
    }

    //statistiques
    public function countAll(): int
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return Immeuble::count();
        }

        // Gestionnaire : seulement ses immeubles
        return Immeuble::where('manager_id', $user->id)->count();
    }

    // Immeubles actifs
    public function countActifs(): int
    {
        $user = Auth::user();

        $query = Immeuble::where('status', 'actif');

        if ($user->role === 'gestionnaire') {
            $query->where('manager_id', $user->id);
        }

        return $query->count();
    }

    // Immeubles en maintenance
    public function countEnMaintenance(): int
    {
        $user = Auth::user();

        $query = Immeuble::where('status', 'en_maintenance');

        if ($user->role === 'gestionnaire') {
            $query->where('manager_id', $user->id);
        }

        return $query->count();
    }

}