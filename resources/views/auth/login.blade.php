<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-8">
        <h2 class="text-3xl font-serif font-bold text-slate-900">Bienvenue</h2>
        <p class="text-slate-500 mt-2">Connectez-vous pour accéder à votre espace cabinet.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Adresse Email</label>
            <div class="mt-1 relative">
                <input id="email" class="block w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors shadow-sm" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="avocat@lexora.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-slate-700">Mot de passe</label>
                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-brand-DEFAULT hover:text-brand-dark transition-colors" href="{{ route('password.request') }}">
                        Mot de passe oublié ?
                    </a>
                @endif
            </div>
            <div class="mt-1 relative">
                <input id="password" class="block w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors shadow-sm"
                                type="password"
                                name="password"
                                required autocomplete="current-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors" name="remember">
            <label for="remember_me" class="ms-2 block text-sm text-slate-600">
                Se souvenir de moi
            </label>
        </div>

        <div>
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-brand-DEFAULT hover:bg-brand-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-DEFAULT transition-colors duration-200">
                Se connecter
            </button>
        </div>
        
        @if (Route::has('register'))
            <p class="text-center text-sm text-slate-500">
                Nouveau sur Lexora ? 
                <a href="{{ route('register') }}" class="font-medium text-brand-DEFAULT hover:text-brand-dark transition-colors">Créer un compte</a>
            </p>
        @endif
    </form>
</x-guest-layout>
