<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $roles = Role::whereIn('nom', ['Avocat', 'Assistant Juridique'])->get();

        return view('auth.register', [
            'roles' => $roles,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Support graceful fallback if 'name' is provided instead of 'nom' / 'prenom'
        if ($request->filled('name') && (! $request->filled('nom') || ! $request->filled('prenom'))) {
            $parts = explode(' ', trim((string) $request->name), 2);
            $request->merge([
                'prenom' => $parts[0] ?? '',
                'nom' => $parts[1] ?? ($parts[0] ?? ''),
            ]);
        }

        // Rôle par défaut : Avocat si non spécifié
        if (! $request->filled('role_id')) {
            $defaultRole = Role::firstOrCreate(['nom' => 'Avocat']);
            $request->merge(['role_id' => $defaultRole->id]);
        }

        $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', 'exists:roles,id'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
