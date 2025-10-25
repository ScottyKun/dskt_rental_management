<?php
namespace App\Repositories;

use App\Models\User;

class ManagerRepository{
    
    //afficher tous les locataires d'un gestionnaire
    public function getLocatairesByManager(int $managerId,int $page=7)
    {
        return User::where('manager_id', $managerId)
                   ->where('role', 'locataire')
                   ->orderBy('created_at', 'desc')->paginate($page);
    }

    //afficher les locataire en attente de validation
    public function getPendingLocatairesByManager(int $page=7)
    {
        return User::where('role', 'locataire')
                   ->where('manager_id', null)
                   ->where('is_validated', false)
                   ->orderBy('created_at', 'desc')->paginate($page);
    }

    //creer un locataire avec gestionnaire
    public function createLocataire(array $data, int $managerId)
    {
        $data['role'] = 'locataire';
        $data['manager_id'] = $managerId;
        return User::create($data);
    }

    //mettre a jour un locataire
    public function updateLocataire(int $id, array $data, int $managerId): bool
    {
        $locataire = User::where('id', $id)
                         ->where('manager_id', $managerId)
                         ->where('role', 'locataire')
                         ->first();

        if (!$locataire) return false;
        return $locataire->update($data);
    }

    //supprimer un locataire
    public function deleteLocataire(int $id, int $managerId): bool
    {
        $locataire = User::where('id', $id)
                         ->where('manager_id', $managerId)
                         ->where('role', 'locataire')
                         ->first();

        if (!$locataire) return false;
        return $locataire->delete();
    }

    //rechercher un locataire
    public function searchLocataires(?string $term = null, int $managerId, int $perPage = 7)
    {
        $query = User::where('role', 'locataire')
                    ->where('manager_id', $managerId);

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                ->orWhere('surname', 'LIKE', "%{$term}%")
                ->orWhere('email', 'LIKE', "%{$term}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    //valider un locataire
     public function validateLocataire(int $id, int $managerId): bool
    {
        $locataire = User::where('id', $id)
                        ->where('role', 'locataire')
                        ->first();

        // Si aucun locataire trouvé
        if (!$locataire) {
            return false;
        }

        // Si le locataire n’a pas encore de gestionnaire
        if (is_null($locataire->manager_id)) {
            $locataire->manager_id=$managerId;
        }

        // Si le gestionnaire connecté n’est pas celui du locataire
        if ($locataire->manager_id !== $managerId) {
            return false;
        }

        // Si tout est bon, on valide le locataire
        $locataire->is_validated = true;

        return $locataire->save();
    }

    //desactiver un locataire
    public function desactiverLocataire(int $id, int $managerId): bool
    {
        $locataire = User::where('id', $id)
                        ->where('role', 'locataire')
                        ->where('manager_id', $managerId)
                        ->first();

        if (!$locataire) {
            return false;
        }

        $locataire->is_validated = false;
        return $locataire->save();
    }

    //afficher tous les locataires d'un gestionnaire pour selecteur(appartement)
    public function getLocataires(int $managerId)
    {
        return User::where('manager_id', $managerId)
                ->where('role', 'locataire')
                ->where('is_validated', true)
                ->whereDoesntHave('appartement')
                ->orderBy('name', 'asc')
                ->get();
    } 

    //statistiques
    public function getStats(int $managerId): array
    {
        $query = User::where('role', 'locataire')
                    ->where('manager_id', $managerId);

        $total = (clone $query)->count();
        $pending = (clone $query)->where('is_validated', false)->count();
        $active = (clone $query)->where('is_validated', true)->count();
        $inactive = $total-$active;

        return [
            'total' => $total,
            'pending' => $pending,
            'active' => $active,
            'inactive' => $inactive,
        ];
    }

    
}