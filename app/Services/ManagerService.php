<?php
namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Repositories\ManagerRepository;

class ManagerService{
    protected $managerRepository;
    public function __construct(ManagerRepository $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }

    //all by manager
    public function allLocatairesByManager(int $managerId)
    {
        return $this->managerRepository->getLocatairesByManager($managerId);
    }

    //all for approval
    public function pendingLocataires()
    {
        return $this->managerRepository->getPendingLocatairesByManager();
    }

    //create
    public function createLocataire(array $data, int $managerId)
    {
        if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
        }
        $data['name'] = e($data['name']);
        $data['email'] = e($data['email']);
        $data['surname'] = e($data['surname']);
        $data['phone'] = e($data['phone']);
        $data['address'] = e($data['address']);
        $data['is_validated'] = true;

        return $this->managerRepository->createLocataire($data, $managerId);
    }

    //update
    public function updateLocataire(int $id, array $data, int $managerId): bool
    {
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        }
        else{
            unset($data['password']);
        }
        return $this->managerRepository->updateLocataire($id, $data, $managerId);
    }

    //delete
    public function deleteLocataire(int $id, int $managerId): bool
    {
        return $this->managerRepository->deleteLocataire($id, $managerId);
    }

    //search
    public function searchLocataires(?string $term, int $managerId)
    {
        return $this->managerRepository->searchLocataires($term, $managerId);
    }

    //activate
    public function activateLocataire(int $id, int $managerId): bool
    {
        return $this->managerRepository->validateLocataire($id, $managerId);
    }

    //deactivate
    public function deactivateLocataire(int $id, int $managerId): bool
    {
        return $this->managerRepository->desactiverLocataire($id, $managerId);
    }

    //liste locataires
    public function getlocataires(int $managerId){
        return $this->managerRepository->getlocataires($managerId);
    }

    //statistiques
    public function getStats(int $managerId): array
    {
        return $this->managerRepository->getStats($managerId);
    }

}