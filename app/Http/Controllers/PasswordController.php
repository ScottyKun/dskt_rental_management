<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

class PasswordController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showChangeForm()
    {
        return view('auth.passwords.change');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8|same:password',
        ]);

        $this->authService->changePassword($request->only('password', 'password_confirmation'));

        return redirect()->route('dashboard')
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }
}