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
    /**
     * Affiche la liste des utilisateurs et leurs rôles.
     */
    public function index(Request $request): View
    {
        $query = User::with('role')->latest();

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('search')) {
            $query->recherche((string) $request->input('search'));
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::orderBy('nom')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Met à jour le rôle d'un utilisateur.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $newRole = Role::findOrFail($request->role_id);

        // Sécurité : Empêcher de rétrograder le dernier administrateur du cabinet
        if ($user->isAdministrateur() && $newRole->nom !== 'Administrateur') {
            $adminCount = User::whereHas('role', fn ($q) => $q->where('nom', 'Administrateur'))->count();
            if ($adminCount <= 1) {
                return back()->withErrors([
                    'role_id' => 'Impossible de rétrograder le seul administrateur de la plateforme.',
                ]);
            }
        }

        $user->update(['role_id' => $newRole->id]);

        return back()->with('status', "Le rôle de l'utilisateur {$user->nom_complet} a été modifié en « {$newRole->nom} » avec succès.");
    }
}
