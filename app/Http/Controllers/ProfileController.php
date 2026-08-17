<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\UserService;

class ProfileController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('auth');
    }

    // Formulaire d'edition de son PROPRE profil (tous roles confondus)
    public function edit()
    {
        $user = Auth::user();

        return view('users.editProfile', compact('user'));
    }

    // Mise a jour de son PROPRE profil.
    // Volontairement restreint : ni role, ni manager_id, ni password ici —

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'surname' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'cni_number' => 'nullable|string|max:50',
            'profession' => 'nullable|string|max:100',
        ]);

        $this->userService->update($user->id, $validated);

        return redirect()->route('users.show', $user->id)
            ->with('success', 'Vos informations ont été mises à jour.');
    }
}