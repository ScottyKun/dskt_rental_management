<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckMustChangePassword
{
    
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // On ne redirige PAS si l'utilisateur est déjà sur la page de changement de mot de passe
        if ($user && $user->must_change_password && !$request->is('password/change', 'password/change/*')) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
