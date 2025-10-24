<?php
namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Repositories\UserRepository;

class AuthService{
    protected $userRepository;
    protected $messageService;
    public function __construct(UserRepository $userRepository, MessageService $messageService)
    {
        $this->userRepository = $userRepository;
        $this->messageService = $messageService;
    }

    //enregistrer un nouvel utilisateur
    public function register(array $data,?User $admin = null): User
    {
        // Si c'est un locataire, il doit être validé plus tard
        $isValidated = $data['role'] === 'locataire' ? false : true;

        //creation de l'utilisateur
        $data['password'] = Hash::make($data['password']);
        $data['name'] = e($data['name']);
        $data['surname'] = e($data['surname']);
        $data['email'] = e($data['email']);
        $data['is_validated'] = $isValidated;
        $user= $this->userRepository->create($data);

        //creation du message de validation
        if ($user->role === 'locataire') {
            $title = 'Nouvelle inscription';
            $content = "Un nouveau locataire nommé {$user->name} {$user->surname} s'est inscrit et attend validation.";
            $this->messageService->sendToAdminsAndManagers($user->id, $title, $content);
        }

       return $user;
    }

    //connecter l'utilisateur
    public function login(array $credentials): bool{
        if (Auth::attempt($credentials)) {
            session()->regenerate();
            return true;
        }
        throw ValidationException::withMessages([
            '' => __('The provided credentials are incorrect.'),
        ]);

    }

    //deconnecter l'utilisateur
    public function logout(): void{
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    //recuperer l'utilisateur actuellement connecté
    public function currentuser(): ?User{
        return Auth::user();
    }

    //changer le mot de passe de l'utilisateur
    public function changePassword(array $data): void   
    {
        $user = $this->currentUser();
        if (!$user) {
            throw ValidationException::withMessages([
                '' => 'Utilisateur non authentifié.'
            ]);
        }

        // Mise à jour du mot de passe dans la base
        $this->userRepository->update($user->id, [
            'password' => Hash::make($data['password']),
            'must_change_password' => false,
        ]);

        // Recharge le modèle à jour depuis la base
        $user->refresh();

        // Régénère la session pour éviter les conflits de redirection
        session()->forget('must_change_password');
    }

    
}
