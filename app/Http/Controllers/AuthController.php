<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

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
            'role'=>'required|in:locataire,admin,gestionnaire',

        ]);

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

        return redirect()->route('dashboard')->with('success','Successfully logged in');
    }

    public function dashboard()
    {
        $user = $this->authService->currentuser();
        return match($user->role) {
            'admin' => view('dashboards.admin', compact('user')),
            'gestionnaire' => view('dashboards.gestionnaire', compact('user')),
            'locataire' => view('dashboards.locataire', compact('user')),
            default => abort(403, 'Unauthorized action.'),
        };
    }

    public function logout(){
        $this->authService->logout();
        return redirect()->route('login')->with('success','Successfully logged out');
    }
    
}