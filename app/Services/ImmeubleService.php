<?php
namespace App\Services;


use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use App\Repositories\ImmeubleRepository;
use App\Repositories\UserRepository;

class ImmeubleService{

    protected $immeubleRepository;
    protected $userRepository;
    public function __construct(ImmeubleRepository $immeubleRepository, UserRepository $userRepository)
    {
        $this->immeubleRepository = $immeubleRepository;
        $this->userRepository = $userRepository;
    }

    //Creer un immeuble
    public function create(array $data)
    {
        $user=Auth::user();

        $data['creator_id'] = $user->id;
        $data['name'] = e($data['name']);
        $data['address'] = e($data['address']);
        $data['town'] = e($data['town']);
        $data['description'] = e($data['description'] ?? '');
        $data['nb_apartments'] = $data['nb_apartments'] ?? 0;
        $data['nb_available'] = $data['nb_available'] ?? $data['nb_apartments'] ;
        $data['nb_occupied'] = $data['nb_occupied'] ?? 0;
        $data['status'] = $data['status'] ?? 'actif'; // ou inactif selon ta logique

        // Vérifie qu’un gestionnaire est bien défini
        if (empty($data['manager_id'])) {
            throw ValidationException::withMessages([
                'manager_id' => 'Un gestionnaire doit être attribué à l\'immeuble.'
            ]);
        }

        return $this->immeubleRepository->create($data);
        
    }

    //supprimer un immeuble
    public function delete(int $id): bool
    {
        $user = Auth::user();

        // Seul un admin peut supprimer
        if ($user->role !== 'admin') {
            throw ValidationException::withMessages([
                'permission' => 'Seul un administrateur peut supprimer un immeuble.'
            ]);
        }

        return $this->immeubleRepository->delete($id);
    }

    //update un immeuble
    public function update(int $id, array $data): bool
    {
         $user = Auth::user();
        $immeuble = $this->immeubleRepository->findById($id);

        if (!$immeuble) {
            throw ValidationException::withMessages([
                'immeuble' => 'Immeuble introuvable.'
            ]);
        }

        // Admin : peut tout modifier
        // Gestionnaire : ne peut modifier que ses immeubles
        if ($user->role === 'gestionnaire' && $immeuble->manager_id !== $user->id) {
            throw ValidationException::withMessages([
                'permission' => 'Vous ne pouvez modifier que les immeubles que vous gérez.'
            ]);
        }

        $data['name'] = e($data['name'] ?? $immeuble->name);
        $data['address'] = e($data['address'] ?? $immeuble->address);
        $data['town'] = e($data['town'] ?? $immeuble->town);
        $data['description'] = e($data['description'] ?? $immeuble->description);
        $data['nb_apartments'] = e($data['nb_apartments'] ?? $immeuble->nb_apartments);
        $data['nb_available'] = $data['nb_available'] ?? $data['nb_apartments'] ;

        return $this->immeubleRepository->update($id, $data);
    }

    //afficher les immeubles
    public function all(){
        return $this->immeubleRepository->all(10);
    }

    //afficher selon gestionnaire
    public function allByManager(int $managerId){
        return $this->immeubleRepository->allByManager($managerId,10);
    }

    //rechercher
    public function search(?string $term )
    {
        return $this->immeubleRepository->search($term,10);
    }

    //afficher un immeuble par id
    public function findById(int $id)
    {
        return $this->immeubleRepository->findById($id);
    }

    //les managers
    public function managers()
    {
        return $this->userRepository->findByManagers();
    }

    //getstats
    public function getStats()
    {
        return [
            'total' => $this->immeubleRepository->countAll(),
            'actifs' => $this->immeubleRepository->countActifs(),
            'maintenance' => $this->immeubleRepository->countEnMaintenance(),
        ];
    }
}