@props(['statut'])

@php
    $classes = match ($statut) {
        'En cours', 'Terminée', 'Payée' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
        'Gagné' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        'Prévue' => 'bg-brand-50 text-brand-DEFAULT border-brand-100',
        'Annulée', 'Perdu' => 'bg-red-50 text-red-600 border-red-100',
        'Non payée' => 'bg-amber-50 text-amber-600 border-amber-100',
        'Fermé' => 'bg-slate-100 text-slate-600 border-slate-200',
        default => 'bg-slate-100 text-slate-600 border-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => 'px-2.5 py-1 rounded-full text-xs font-medium border '.$classes]) }}>{{ $statut }}</span>