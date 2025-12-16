<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CommercantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est authentifié ET a un rôle de commerçant
        if (auth()->check() && $this->isCommercant(auth()->user())) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Accès réservé aux commerçants.');
    }

    /**
     * Vérifie si l'utilisateur a un rôle de commerçant
     */
    private function isCommercant($user): bool
    {
        $commercantRoles = [
            'commercant',
            'merchant', 
            'vendeur',
            'commercial',
            'business',
            'entreprise'
        ];

        return in_array($user->role, $commercantRoles);
    }
}