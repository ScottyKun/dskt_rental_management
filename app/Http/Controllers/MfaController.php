<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\MfaService;

class MfaController extends Controller
{
    protected $mfaService;

    public function __construct(MfaService $mfaService)
    {
        $this->mfaService = $mfaService;
    }

    public function showChallenge()
    {
        $user = Auth::user();

        // Déjà vérifié ? On ne devrait pas être ici (le middleware l'empêche),
        // mais on redirige proprement au cas où.
        if (session('mfa_verified') === true) {
            return redirect()->route('dashboard');
        }

        if (!$this->mfaService->hasActiveCode($user)) {
            $this->mfaService->generateAndSend($user);
        }

        return view('auth.mfa-challenge', [
            'maskedEmail' => $this->maskEmail($user->email),
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $result = $this->mfaService->verify(Auth::user(), $request->input('code'));

        if (!$result['success']) {
            return back()->withErrors(['code' => $result['message']]);
        }

        session(['mfa_verified' => true]);
        session()->regenerate();

        return redirect()->intended(route('dashboard'))->with('success', 'Connexion vérifiée avec succès.');
    }

    public function resend(Request $request)
    {
        $this->mfaService->generateAndSend(Auth::user());

        return back()->with('success', 'Un nouveau code vous a été envoyé par e-mail.');
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email);
        $visible = mb_substr($local, 0, 2);
        return $visible . str_repeat('*', max(strlen($local) - 2, 3)) . '@' . $domain;
    }
}
