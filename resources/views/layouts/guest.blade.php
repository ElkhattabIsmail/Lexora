<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Lexora') }} - Gestion Cabinet d'Avocats</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-900 bg-slate-50">
        <div class="flex min-h-screen">
            <!-- Left Side - Branding & Aesthetics -->
            <div class="hidden lg:flex lg:w-1/2 bg-slate-900 relative overflow-hidden flex-col justify-between p-12">
                <!-- Abstract Background Shapes/Gradients -->
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-brand-dark via-slate-900 to-slate-850"></div>
                    <div class="absolute -top-48 -left-48 w-96 h-96 bg-brand-light rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
                    <div class="absolute top-1/2 left-1/2 w-72 h-72 bg-gold-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
                </div>

                <div class="relative z-10">
                    <a href="/" class="flex items-center gap-3">
                        <x-application-logo class="w-12 h-12 text-gold-500 fill-current" />
                        <span class="text-3xl font-serif font-bold text-white tracking-wide">Lexora</span>
                    </a>
                </div>

                <div class="relative z-10 mb-20">
                    <h1 class="text-4xl md:text-5xl font-serif font-bold text-white leading-tight mb-6">
                        L'excellence au service de la justice.
                    </h1>
                    <p class="text-lg text-slate-300 max-w-md font-light leading-relaxed">
                        Gérez vos dossiers, clients, audiences et facturations depuis une plateforme unifiée, sécurisée et pensée pour les professionnels du droit.
                    </p>
                </div>
                
                <div class="relative z-10 flex items-center gap-4 text-sm text-slate-400">
                    <span>&copy; {{ date('Y') }} Lexora. Tous droits réservés.</span>
                    <a href="#" class="hover:text-gold-400 transition-colors">Confidentialité</a>
                    <a href="#" class="hover:text-gold-400 transition-colors">Conditions</a>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 relative">
                <!-- Mobile Logo -->
                <div class="absolute top-8 left-8 lg:hidden flex items-center gap-2">
                    <x-application-logo class="w-8 h-8 text-brand-DEFAULT fill-current" />
                    <span class="text-xl font-serif font-bold text-slate-900">Lexora</span>
                </div>

                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
