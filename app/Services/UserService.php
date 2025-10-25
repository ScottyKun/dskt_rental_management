<?php
namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Repositories\UserRepository;

class UserService{
    protected $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    //Creer un utilisateur
    public function create(array $data,?User $admin = null)
    {
       
        // Si c'est un gestionnaire, seul un admin peut créer
        if ($data['role'] === 'gestionnaire' && (!$admin || $admin->role !== 'admin')) {
            throw ValidationException::withMessages([
                'role' => 'Seul un administrateur peut créer un gestionnaire.'
            ]);
        }

        $data['password'] = Hash::make($data['password']);
        $data['name'] = e($data['name']);
        $data['email'] = e($data['email']);
        $data['surname'] = e($data['surname']);
        $data['phone'] = e($data['phone']);
        $data['address'] = e($data['address']);
        $data['is_validated'] = true;
        $data['manager_id'] = e($data['manager_id']);

        // Assigner un manager si c'est un locataire
        if ($data['role'] === 'locataire' && !isset($data['manager_id'])) {
            throw ValidationException::withMessages([
                'manager_id' => 'Selectionner un gestionnaire.'
            ]);
        }

        return $this->userRepository->create($data);
    }

    //Supprimer un utilisateur
    public function delete(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    //Mettre a jour un utilisateur
    public function update(int $id, array $data): bool
    {
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        }
        else{
            unset($data['password']);
        }  
        
        // Assigner un manager si c'est un locataire
        if ($data['role'] === 'locataire' && !isset($data['manager_id'])) {
            throw ValidationException::withMessages([
                'manager_id' => 'Selectionner un gestionnaire.'
            ]);
        }

        return $this->userRepository->update($id, $data);
    }

    //rechercher un utilisateur
    public function research(?string $query)
    {
        return $this->userRepository->rechercher($query);
    }

    //valider un utilisateur
    public function validate(int $id): bool
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }

        // Si c'est un locataire
        if ($user->role === 'locataire') {
            if (!$user->manager_id) {
                throw ValidationException::withMessages([
                    'manager_id' => 'Veuillez assigner un gestionnaire avant de valider ce locataire.'
                ]);
            }
        }

        // Sinon
        return $this->userRepository->valider($id);
    }

    //desactiver un utilisateur
    public function deactivate(int $id):bool{
        return $this->userRepository->desactiver($id);
    }

    public function countAll()
    {
        return User::count();
    }

    public function countByRole(string $role)
    {
        return User::where('role', $role)->count();
    }

    public function countPendingValidations()
    {
        return User::where('is_validated', false)->count();
    }

    //les locataires sans apparts
    public function getLocatairesSansAppartement()
    {
        return $this->userRepository->getLocatairesSansAppartement();
    }

    //liste des gestionnaires
    public function managers(){
        return $this->userRepository->findByManagers();
    }

}