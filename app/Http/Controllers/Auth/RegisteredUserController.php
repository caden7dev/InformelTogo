<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    // Affiche le formulaire d'inscription
    public function create()
    {
        return view('auth.register');
    }

    // Traitement de l'inscription
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // ✅ EN CLAIR - le mutator s'occupe du hachage
            'role' => 'commercant', // ⚠️ Ajoutez cette ligne si c'est pour les commerçants
            'region' => $request->region ?? 'Non spécifié', // ⚠️ Ajoutez si nécessaire
            'secteur' => $request->secteur ?? 'Non spécifié', // ⚠️ Ajoutez si nécessaire
        ]);

        // Optionnel : Connecter automatiquement l'utilisateur
        // Auth::login($user);

        return redirect()->route('login')->with('success', 'Inscription réussie ! Veuillez vous connecter.');
    }
}