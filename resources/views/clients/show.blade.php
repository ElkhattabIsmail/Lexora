<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('clients.index') }}" class="text-slate-400 hover:text-brand-DEFAULT transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">
                    {{ $client->nom_complet }}
                </h2>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('clients.edit', $client) }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    Modifier
                </a>
                <a href="{{ route('dossiers.create', ['client_id' => $client->id]) }}" class="px-4 py-2 bg-brand-DEFAULT text-white rounded-lg text-sm font-medium hover:bg-brand-dark transition-colors shadow-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Nouveau Dossier
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left: Profile Card --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col items-center text-center">
                        <div class="w-24 h-24 rounded-full {{ $client->isEntreprise() ? 'bg-gold-100 text-gold-600' : 'bg-brand-100 text-brand-DEFAULT' }} flex items-center justify-center font-bold text-3xl mb-4">
                            {{ strtoupper(mb_substr($client->nom, 0, 1)) }}{{ $client->prenom ? strtoupper(mb_substr($client->prenom, 0, 1)) : '' }}
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 font-serif">{{ $client->nom_complet }}</h3>
                        <span class="px-3 py-1 mt-2 rounded-full text-xs font-medium bg-slate-100 text-slate-700">{{ $client->type }}</span>

                        <div class="w-full mt-6 space-y-4 text-sm text-left">
                            @if ($client->email)
                                <div class="flex items-center gap-3 text-slate-600">
                                    <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    <span>{{ $client->email }}</span>
                                </div>
                            @endif
                            @if ($client->telephone)
                                <div class="flex items-center gap-3 text-slate-600">
                                    <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                    <span>{{ $client->telephone }}</span>
                                </div>
                            @endif
                            @if ($client->adresse)
                                <div class="flex items-start gap-3 text-slate-600">
                                    <svg class="w-5 h-5 text-slate-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <span class="whitespace-pre-line">{{ $client->adresse }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Stats rapides --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-slate-900">{{ $client->dossiers->count() }}</p>
                            <p class="text-xs text-slate-500 mt-1">Dossiers</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-slate-900">{{ $client->factures->count() }}</p>
                            <p class="text-xs text-slate-500 mt-1">Factures</p>
                        </div>
                    </div>
                </div>

                {{-- Right: Tabs --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden" x-data="{ onglet: 'dossiers' }">
                        {{-- Tab nav --}}
                        <div class="border-b border-slate-100 px-6 pt-4 flex gap-6">
                            <button type="button" @click="onglet = 'dossiers'"
                                    :class="onglet === 'dossiers' ? 'border-brand-DEFAULT text-brand-DEFAULT' : 'border-transparent text-slate-500 hover:text-slate-700'"
                                    class="pb-3 border-b-2 font-medium text-sm transition-colors">
                                Dossiers ({{ $client->dossiers->count() }})
                            </button>
                            <button type="button" @click="onglet = 'factures'"
                                    :class="onglet === 'factures' ? 'border-brand-DEFAULT text-brand-DEFAULT' : 'border-transparent text-slate-500 hover:text-slate-700'"
                                    class="pb-3 border-b-2 font-medium text-sm transition-colors">
                                Factures ({{ $client->factures->count() }})
                            </button>
                        </div>

                        {{-- Dossiers list --}}
                        <div class="p-6" x-show="onglet === 'dossiers'">
                            <div class="space-y-4">
                                @forelse ($client->dossiers as $dossier)
                                    <a href="{{ route('dossiers.show', $dossier) }}" class="block p-5 border border-slate-100 rounded-xl hover:border-slate-300 hover:shadow-sm transition-all group">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-center gap-3">
                                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-md text-xs font-bold">{{ $dossier->numero_dossier }}</span>
                                                <h4 class="font-bold text-slate-900 group-hover:text-brand-DEFAULT transition-colors">{{ $dossier->type_affaire }}</h4>
                                            </div>
                                            <x-status-badge :statut="$dossier->statut" />
                                        </div>
                                        <div class="flex gap-4 text-xs text-slate-400 mt-2">
                                            <div class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                Ouvert le {{ $dossier->date_ouverture?->format('d M Y') }}
                                            </div>
                                            @if ($dossier->avocat)
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                    {{ $dossier->avocat->nom_complet }}
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-10 text-slate-400">
                                        <svg class="w-10 h-10 mx-auto mb-3 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        <p class="text-sm">Aucun dossier pour ce client.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Factures list --}}
                        <div class="p-6" x-show="onglet === 'factures'" x-cloak>
                            <div class="space-y-4">
                                @forelse ($client->factures as $facture)
                                    <a href="{{ route('factures.show', $facture) }}" class="block p-5 border border-slate-100 rounded-xl hover:border-slate-300 hover:shadow-sm transition-all group">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-center gap-3">
                                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-md text-xs font-bold">{{ $facture->numero_facture }}</span>
                                                <h4 class="font-bold text-slate-900 group-hover:text-brand-DEFAULT transition-colors">Facture</h4>
                                            </div>
                                            <x-status-badge :statut="$facture->statut" />
                                        </div>
                                        <div class="flex gap-4 text-xs text-slate-400 mt-2">
                                            <div class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                Émise le {{ $facture->date_facture?->format('d M Y') }}
                                            </div>
                                            <div class="flex items-center gap-1 font-semibold text-slate-600">
                                                {{ number_format($facture->montant, 2, ',', ' ') }} €
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-10 text-slate-400">
                                        <svg class="w-10 h-10 mx-auto mb-3 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <p class="text-sm">Aucune facture pour ce client.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
