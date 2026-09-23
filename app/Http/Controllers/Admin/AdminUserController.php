<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        // ---------------------------------------------------------------------
        // Construction de la requête avec Eager Loading de la relation 'role'
        // pour charger les rôles de chaque utilisateur sans N+1 queries.
        // ---------------------------------------------------------------------
        $query = User::with('role')->latest();

        // ---------------------------------------------------------------------
        // filled('role_id') : Vérifie que le paramètre est présent dans la requête et non vide
        // ---------------------------------------------------------------------
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // ---------------------------------------------------------------------
        // scopeRecherche(...) : Filtre par nom, prénom ou email de l'utilisateur
        // ---------------------------------------------------------------------
        if ($request->filled('search')) {
            $query->recherche((string) $request->input('search'));
        }

        // ---------------------------------------------------------------------
        // Pagination à 15 utilisateurs avec conservation des filtres de recherche dans l'URL
        // ---------------------------------------------------------------------
        $users = $query->paginate(15)->withQueryString();

        // ---------------------------------------------------------------------
        // Récupère la liste de tous les rôles pour alimenter le menu déroulant
        // ---------------------------------------------------------------------
        $roles = Role::orderBy('nom')->get();

        // ---------------------------------------------------------------------
        // Affiche la vue d'administration des utilisateurs
        // ---------------------------------------------------------------------
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // Validation inline : vérifie que le role_id existe bien dans la table 'roles'
        // ---------------------------------------------------------------------
        $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        // ---------------------------------------------------------------------
        // findOrFail(...) : Récupère le modèle Role ou renvoie une 404 si introuvable
        // ---------------------------------------------------------------------
        $newRole = Role::findOrFail($request->role_id);

        // ---------------------------------------------------------------------
        // Garde-fou de sécurité métier :
        // Empêche la rétrogradation du dernier Administrateur du cabinet pour ne pas bloquer l'accès.
        // ---------------------------------------------------------------------
        if ($user->loadMissing('role')->isAdministrateur() && $newRole->nom !== 'Administrateur') {
            // whereHas('role', ...) : Compte combien d'administrateurs actifs existent encore en base
            $adminCount = User::whereHas('role', fn ($q) => $q->where('nom', 'Administrateur'))->count();
            if ($adminCount <= 1) {
                return back()->withErrors([
                    'role_id' => 'Impossible de rétrograder le seul administrateur de la plateforme.',
                ]);
            }
        }

        // ---------------------------------------------------------------------
        // Enregistre le nouveau rôle de l'utilisateur
        // ---------------------------------------------------------------------
        $user->update(['role_id' => $newRole->id]);

        // ---------------------------------------------------------------------
        // Redirige vers la page précédente avec confirmation
        // ---------------------------------------------------------------------
        return back()->with('status', "Le rôle de l'utilisateur {$user->nom_complet} a été modifié en « {$newRole->nom} » avec succès.");
    }
}
