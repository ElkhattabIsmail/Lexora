<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Lexora') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css'])
    </head>
    <body class="font-sans antialiased bg-slate-900 text-slate-100 min-h-screen flex flex-col">
        <header class="relative z-20 w-full">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex items-center justify-between gap-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 shrink-0">
                    <span class="w-9 h-9 rounded-lg bg-brand-DEFAULT flex items-center justify-center shadow-md shadow-brand-DEFAULT/30">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    </span>
                    <span class="font-serif font-bold text-xl text-white tracking-tight">{{ config('app.name', 'Lexora') }}</span>
                </a>
                @if (Route::has('login'))
                    <div class="flex items-center gap-2 sm:gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-secondary">
                                Tableau de bord
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary">
                                Se connecter
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-ghost-dark">
                                    S'inscrire
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </nav>
        </header>

        <main class="flex-1 relative overflow-hidden">
            <!-- Ambient background -->
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute inset-0 bg-gradient-to-br from-brand-dark/40 via-transparent to-slate-850"></div>
                <div class="absolute -top-32 -right-32 w-[34rem] h-[34rem] bg-brand-light/15 rounded-full blur-3xl animate-blob"></div>
                <div class="absolute top-1/3 -left-40 w-96 h-96 bg-gold-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
                <div class="max-w-3xl">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gold-500/10 text-gold-400 text-xs font-medium border border-gold-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        Cabinet d'avocats
                    </span>
                    <h1 class="mt-6 font-serif font-bold text-4xl sm:text-5xl text-white leading-tight">
                        Des dossiers juridiques,<br class="hidden sm:block"> enfin <span class="text-brand-light">simplifiés</span>.
                    </h1>
                    <p class="mt-6 text-lg text-slate-300 leading-relaxed">
                        Lexora centralise la gestion de vos clients, dossiers, audiences, documents et factures
                        dans un outil unique, pensé pour les cabinets d'avocats.
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row gap-3 sm:gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-primary btn-lg">
                                Accéder à mon espace
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary btn-lg shadow-lg shadow-brand-DEFAULT/25">
                                Commencer maintenant
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-ghost-dark btn-lg">
                                    Créer un compte
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm transition-colors hover:bg-white/[0.07] hover:border-white/20">
                        <div class="w-10 h-10 rounded-lg bg-brand-DEFAULT/20 text-brand-light flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <h3 class="font-serif font-bold text-lg text-white">Gestion des dossiers</h3>
                        <p class="mt-2 text-sm text-slate-400">Suivez chaque affaire, son statut, son historique et les avocats responsables.</p>
                    </div>
                    <div class="p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm transition-colors hover:bg-white/[0.07] hover:border-white/20">
                        <div class="w-10 h-10 rounded-lg bg-gold-500/20 text-gold-400 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <h3 class="font-serif font-bold text-lg text-white">Audiences & rappels</h3>
                        <p class="mt-2 text-sm text-slate-400">Planifiez vos audiences et recevez des rappels automatiques par email.</p>
                    </div>
                    <div class="p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm transition-colors hover:bg-white/[0.07] hover:border-white/20">
                        <div class="w-10 h-10 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h3 class="font-serif font-bold text-lg text-white">Facturation</h3>
                        <p class="mt-2 text-sm text-slate-400">Émettez des factures, enregistrez les paiements et suivez les soldes restants.</p>
                    </div>
                </div>
            </div>
        </main>

        <footer class="relative z-10 border-t border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center justify-between text-sm text-slate-500">
                <span>&copy; {{ date('Y') }} {{ config('app.name', 'Lexora') }} — Tous droits réservés.</span>
                <span>v{{ app()->version() }}</span>
            </div>
        </footer>
    </body>
</html>