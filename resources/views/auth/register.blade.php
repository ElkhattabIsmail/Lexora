<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-3xl font-serif font-bold text-slate-900">Créer un compte</h2>
        <p class="text-slate-500 mt-2">Rejoignez Lexora et simplifiez la gestion de votre cabinet.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Nom & Prénom -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="nom" class="block text-sm font-medium text-slate-700">Nom</label>
                <div class="mt-1 relative">
                    <input id="nom" class="block w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors shadow-sm" type="text" name="nom" value="{{ old('nom') }}" required autofocus autocomplete="family-name" placeholder="Berrada" />
                </div>
                <x-input-error :messages="$errors->get('nom')" class="mt-1.5" />
            </div>

            <div>
                <label for="prenom" class="block text-sm font-medium text-slate-700">Prénom</label>
                <div class="mt-1 relative">
                    <input id="prenom" class="block w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors shadow-sm" type="text" name="prenom" value="{{ old('prenom') }}" required autocomplete="given-name" placeholder="Youssef" />
                </div>
                <x-input-error :messages="$errors->get('prenom')" class="mt-1.5" />
            </div>
        </div>

        <!-- Téléphone & Rôle -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="telephone" class="block text-sm font-medium text-slate-700">Téléphone</label>
                <div class="mt-1 relative">
                    <input id="telephone" class="block w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors shadow-sm" type="text" name="telephone" value="{{ old('telephone') }}" autocomplete="tel" placeholder="+212 6 00 00 00 00" />
                </div>
                <x-input-error :messages="$errors->get('telephone')" class="mt-1.5" />
            </div>

            <div>
                <label for="role_id" class="block text-sm font-medium text-slate-700">Fonction / Rôle</label>
                <div class="mt-1 relative">
                    <select id="role_id" name="role_id" required class="block w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors shadow-sm">
                        @if(isset($roles) && $roles->isNotEmpty())
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->nom }}
                                </option>
                            @endforeach
                        @else
                            <option value="2">Avocat</option>
                            <option value="3">Assistant Juridique</option>
                        @endif
                    </select>
                </div>
                <x-input-error :messages="$errors->get('role_id')" class="mt-1.5" />
            </div>
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Adresse Email</label>
            <div class="mt-1 relative">
                <input id="email" class="block w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors shadow-sm" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="avocat@lexora.ma" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password & Confirm Password -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Mot de passe</label>
                <div class="mt-1 relative">
                    <input id="password" class="block w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors shadow-sm"
                                    type="password"
                                    name="password"
                                    required autocomplete="new-password" placeholder="••••••••" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirmer</label>
                <div class="mt-1 relative">
                    <input id="password_confirmation" class="block w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors shadow-sm"
                                    type="password"
                                    name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="btn-primary w-full">
                S'inscrire
            </button>
        </div>
        
        <p class="text-center text-sm text-slate-500 pt-2">
            Déjà inscrit ? 
            <a href="{{ route('login') }}" class="font-medium text-brand-DEFAULT hover:text-brand-dark transition-colors">Se connecter</a>
        </p>
    </form>
</x-guest-layout>
