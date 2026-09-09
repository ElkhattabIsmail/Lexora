<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Lexora') }} — Page introuvable</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50">
        <div class="min-h-screen flex items-center justify-center px-4">
            <div class="text-center">
                <p class="text-8xl font-serif font-bold text-slate-300">404</p>
                <h1 class="mt-4 text-2xl font-bold text-slate-900 font-serif">Page introuvable</h1>
                <p class="mt-2 text-slate-500 max-w-md mx-auto">
                    La ressource demandée n'existe pas ou a été déplacée.
                </p>
                <a href="{{ url('/') }}" class="mt-8 inline-block px-5 py-2.5 bg-brand-DEFAULT text-white rounded-lg text-sm font-medium hover:bg-brand-dark transition-colors shadow-sm">
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </body>
</html>