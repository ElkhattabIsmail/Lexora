<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        // ---------------------------------------------------------------------
        // $request->user() : Récupère l'instance du modèle User actuellement authentifié
        // et l'injecte dans la vue de gestion du profil
        // ---------------------------------------------------------------------
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // fill(...) : Hydrate le modèle avec les données validées sans persister immédiatement
        // ---------------------------------------------------------------------
        $request->user()->fill($request->validated());

        // ---------------------------------------------------------------------
        // isDirty('email') :
        // Vérifie si l'adresse e-mail a été modifiée par rapport à la base.
        // Si oui, on invalide la vérification de l'e-mail pour forcer une nouvelle validation.
        // ---------------------------------------------------------------------
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // ---------------------------------------------------------------------
        // Enregistre les modifications en base
        // ---------------------------------------------------------------------
        $request->user()->save();

        // ---------------------------------------------------------------------
        // Redirige vers le formulaire avec un statut flash 'profile-updated'
        // ---------------------------------------------------------------------
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // validateWithBag(...) :
        // Valide le mot de passe actuel dans un sac d'erreurs dédié nommé 'userDeletion'
        // afin de cibler les messages d'erreur spécifiquement dans la modale de suppression
        // ---------------------------------------------------------------------
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // ---------------------------------------------------------------------
        // Déconnexion de la session utilisateur
        // ---------------------------------------------------------------------
        Auth::logout();

        // ---------------------------------------------------------------------
        // Suppression du compte utilisateur en base
        // ---------------------------------------------------------------------
        $user->delete();

        // ---------------------------------------------------------------------
        // Sécurité de session : Invalide la session et régénère le jeton CSRF pour éviter toute réutilisation
        // ---------------------------------------------------------------------
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ---------------------------------------------------------------------
        // Redirection vers la page d'accueil
        // ---------------------------------------------------------------------
        return Redirect::to('/');
    }
}
