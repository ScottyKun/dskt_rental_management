<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureMfaVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && session('mfa_verified') !== true) {
            return redirect()->route('mfa.challenge');
        }

        return $next($request);
    }
}
