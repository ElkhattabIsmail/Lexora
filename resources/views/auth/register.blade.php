<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-3xl font-serif font-bold text-slate-900">Créer un compte</h2>
        <p class="text-slate-500 mt-2">Rejoignez Lexora et simplifiez la gestion de votre cabinet.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Nom complet</label>
            <div class="mt-1 relative">
                <input id="name" class="block w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors shadow-sm" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Me. Jean Dupont" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Adresse Email</label>
            <div class="mt-1 relative">
                <input id="email" class="block w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors shadow-sm" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="avocat@lexora.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700">Mot de passe</label>
            <div class="mt-1 relative">
                <input id="password" class="block w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors shadow-sm"
                                type="password"
                                name="password"
                                required autocomplete="new-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirmer le mot de passe</label>
            <div class="mt-1 relative">
                <input id="password_confirmation" class="block w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors shadow-sm"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-brand-DEFAULT hover:bg-brand-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-DEFAULT transition-colors duration-200">
                S'inscrire
            </button>
        </div>
        
        <p class="text-center text-sm text-slate-500 pt-2">
            Déjà inscrit ? 
            <a href="{{ route('login') }}" class="font-medium text-brand-DEFAULT hover:text-brand-dark transition-colors">Se connecter</a>
        </p>
    </form>
</x-guest-layout>
