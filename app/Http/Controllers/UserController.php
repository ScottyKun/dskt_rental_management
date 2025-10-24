<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\UserService;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('auth');
    }

    // Affiche la liste des utilisateurs et gère la recherche
    public function index(Request $request)
    {
        $query = $request->input('search');
        $users = $this->userService->research($query)->paginate(7); // pagination 10

        // Statistiques
        $stats = [
            'total_users' => $this->userService->countAll(),
            'locataires' => $this->userService->countByRole('locataire'),
            'pending_validations' => $this->userService->countPendingValidations(),
            'gestionnaires' => $this->userService->countByRole('gestionnaire'),
        ];

        return view('users.index', compact('users', 'stats'));
    }


    //retourne le formulaire d'ajout
    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'surname' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role' => 'required|in:locataire,admin,gestionnaire',
        ]);

        $admin = Auth::user(); // utilisateur connecté, pour création de gestionnaire

        $this->userService->create($validated, $admin);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    //retourne le formulaire de modification
    public function edit(int $id)
    {
        $user = $this->userService->research(null)->where('id', $id)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }
        return view('users.edit', compact('user'));
    }

     public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|min:3|max:255',
            'surname' => 'sometimes|string|min:3|max:255',
            'email' => "sometimes|string|email|max:255|unique:users,email,{$id}",
            'password' => 'nullable|string|min:6|confirmed',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
            'role' => 'sometimes|in:locataire,admin,gestionnaire',
        ]);

        $updated = $this->userService->update($id, $validated);

        if (!$updated) {
            return redirect()->back()->with('error', 'User not found.');
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(int $id)
    {
        $deleted = $this->userService->delete($id);

        if (!$deleted) {
            return redirect()->back()->with('error', 'User not found.');
        }

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    // Valider un utilisateur 
    public function validateUser(int $id)
    {
        $validated = $this->userService->validate($id);

        if (!$validated) {
            return redirect()->back()->with('error', 'User not found.');
        }

        return redirect()->route('users.index')->with('success', 'User validated successfully.');
    }

    // Désactiver un utilisateur 
    public function deactivate(int $id)
    {
        $deactivated = $this->userService->deactivate($id);

        if (!$deactivated) {
            return redirect()->back()->with('error', 'User not found.');
        }

        return redirect()->route('users.index')->with('success', 'User deactivated successfully.');
    }

   

}
