<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class checkValidated
{
    
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user() && Auth::user()->role === 'locataire' && !Auth::user()->is_validated) {
        Auth::logout();
        return redirect()->route('login')
            ->withErrors(['validation' => 'Votre compte doit être validé par un administrateur.']);
    }

    return $next($request);
    }
}
