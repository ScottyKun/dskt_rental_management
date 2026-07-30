<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Models\User;
use App\Models\Immeuble;
use App\Models\Appartement;
use App\Models\Contrat;
use App\Models\Payment;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    //Retourne le formulaire d'inscription
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request){
        $validated=$request->validate([
            'name'=>'required|string|min:3|max:255',
            'surname'=>'required|string|min:3|max:255',
            'email'=>'required|string|email|max:255|unique:users',
            'password'=> 'required|min:8|confirmed',
        ]);

        // Le formulaire public ne permet de créer que des comptes locataires.
        // Les comptes admin/gestionnaire sont créés uniquement par un admin déjà authentifié (cf. UserController::store).
        $validated['role'] = 'locataire';

        $this->authService->register($validated);

        return redirect()->route('login')->with('success','Successfully Signed in');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request){
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $this->authService->login($credentials);
        $user = $this->authService->currentuser();

        $redirect = $user->role === 'locataire' ? route('tenant.logement') : route('dashboard');

        return redirect($redirect)->with('success','Successfully logged in');
    }

    public function dashboard()
    {
        $user = $this->authService->currentuser();

        return match($user->role) {
            'admin' => view('dashboards.admin', [
                'user' => $user,
                'stats' => $this->adminStats(),
            ]),
            'gestionnaire' => view('dashboards.gestionnaire', [
                'user' => $user,
                'stats' => $this->gestionnaireStats($user->id),
            ]),
            // Le locataire n'a pas de page d'accueil generique: son "accueil" est directement son logement.
            'locataire' => redirect()->route('tenant.logement'),
            default => abort(403, 'Unauthorized action.'),
        };
    }

    public function logout(){
        $this->authService->logout();
        return redirect()->route('login')->with('success','Successfully logged out');
    }

    private function adminStats(): array
    {
        return [
            'locataires' => User::where('role', 'locataire')->count(),
            'gestionnaires' => User::where('role', 'gestionnaire')->count(),
            'immeubles' => Immeuble::count(),
            'appartements_disponibles' => Appartement::where('status', 'disponible')->count(),
            'appartements_occupes' => Appartement::where('status', 'occupe')->count(),
            'contrats_actifs' => Contrat::where('status', 'actif')->count(),
            'revenus_du_mois' => Payment::confirmed()
                ->whereMonth('paid_at', Carbon::now()->month)
                ->whereYear('paid_at', Carbon::now()->year)
                ->sum('amount'),
            'paiements_en_attente' => Payment::where('status', 'PENDING')->count(),
        ];
    }

    private function gestionnaireStats(int $managerId): array
    {
        return [
            'immeubles' => Immeuble::where('manager_id', $managerId)->count(),
            'appartements_disponibles' => Appartement::whereHas('immeuble', fn($q) => $q->where('manager_id', $managerId))
                ->where('status', 'disponible')->count(),
            'appartements_occupes' => Appartement::whereHas('immeuble', fn($q) => $q->where('manager_id', $managerId))
                ->where('status', 'occupe')->count(),
            'contrats_actifs' => Contrat::where('status', 'actif')
                ->whereHas('appartement.immeuble', fn($q) => $q->where('manager_id', $managerId))
                ->count(),
            'locataires_actifs' => Contrat::where('status', 'actif')
                ->whereHas('appartement.immeuble', fn($q) => $q->where('manager_id', $managerId))
                ->distinct('tenant_id')->count('tenant_id'),
            'revenus_du_mois' => Payment::confirmed()
                ->where('manager_id', $managerId)
                ->whereMonth('paid_at', Carbon::now()->month)
                ->whereYear('paid_at', Carbon::now()->year)
                ->sum('amount'),
            'paiements_en_attente' => Payment::where('manager_id', $managerId)
                ->where('status', 'PENDING')->count(),
        ];
    }
    
}