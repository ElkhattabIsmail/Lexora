<aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-slate-900">
    <div class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <p class="px-4 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
            Menu principal
        </p>

        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3v-6h6v6h3a1 1 0 001-1V10" /></svg>
            {{ __('Tableau de bord') }}
        </x-sidebar-link>

        @if (Auth::user()->hasRole(['Avocat', 'Administrateur']))
            <x-sidebar-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2.13a4 4 0 10-4-4m4 4a4 4 0 10-4-4m4-4h.01" /></svg>
                {{ __('Clients') }}
            </x-sidebar-link>

            <x-sidebar-link :href="route('dossiers.index')" :active="request()->routeIs('dossiers.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" /></svg>
                {{ __('Dossiers') }}
            </x-sidebar-link>

            <x-sidebar-link :href="route('factures.index')" :active="request()->routeIs('factures.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14h6m-6-4h6m-7 10a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V18a2 2 0 01-2 2H9z" /></svg>
                {{ __('Factures') }}
            </x-sidebar-link>
        @endif

        @if (Auth::user()->isAdministrateur())
            <p class="px-4 pt-6 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
                Administration
            </p>

            <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                {{ __('Gestion des rôles') }}
            </x-sidebar-link>
        @endif
    </div>

    <div class="px-4 py-4 border-t border-slate-800">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-full bg-slate-700 text-gold-400 flex items-center justify-center font-bold text-sm shrink-0">
                    {{ mb_substr(Auth::user()->prenom, 0, 1) }}{{ mb_substr(Auth::user()->nom, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ Auth::user()->role?->nom }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Se déconnecter" class="text-slate-400 hover:text-gold-400 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                </button>
            </form>
        </div>
    </div>
</aside>