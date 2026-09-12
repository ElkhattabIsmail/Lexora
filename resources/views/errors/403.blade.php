<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Lexora') }} — Accès refusé</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50">
        <div class="min-h-screen flex items-center justify-center px-4">
            <div class="text-center">
                <p class="text-8xl font-serif font-bold text-slate-300">403</p>
                <h1 class="mt-4 text-2xl font-bold text-slate-900 font-serif">Accès non autorisé</h1>
                <p class="mt-2 text-slate-500 max-w-md mx-auto">
                    Vous ne disposez pas des privilèges nécessaires pour accéder à cette ressource.
                </p>
                <a href="{{ route('dashboard') }}" class="btn-primary inline-block mt-8">
                    Retour au tableau de bord
                </a>
            </div>
        </div>
    </body>
</html>