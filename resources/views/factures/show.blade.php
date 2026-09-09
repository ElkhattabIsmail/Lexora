<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('factures.index') }}" class="text-slate-400 hover:text-brand-DEFAULT transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <p class="text-xs text-slate-500 font-mono">{{ $facture->numero_facture }}</p>
                    <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">Facture</h2>
                </div>
            </div>
            <a href="{{ route('factures.edit', $facture) }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                Modifier
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left: Summary --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-4">
                        <h3 class="font-bold text-slate-900 text-lg font-serif">Résumé</h3>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Statut</span>
                            <x-status-badge :statut="$facture->statut" />
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Client</span>
                            <a href="{{ route('clients.show', $facture->client) }}" class="font-medium text-brand-DEFAULT hover:underline">{{ $facture->client->nom_complet }}</a>
                        </div>
                        @if ($facture->dossier)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">Dossier</span>
                                <a href="{{ route('dossiers.show', $facture->dossier) }}" class="font-medium text-brand-DEFAULT hover:underline">{{ $facture->dossier->numero_dossier }}</a>
                            </div>
                        @endif
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Date</span>
                            <span class="text-slate-700">{{ $facture->date_facture?->format('d/m/Y') }}</span>
                        </div>
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-sm text-slate-500">Montant</span>
                            <span class="text-xl font-bold text-slate-900">{{ number_format($facture->montant, 2, ',', ' ') }} €</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Payé</span>
                            <span class="font-medium text-emerald-600">{{ number_format((float) $facture->montant - (float) $facture->montant_restant, 2, ',', ' ') }} €</span>
                        </div>
                        @if ((float) $facture->montant_restant > 0)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">Restant dû</span>
                                <span class="font-medium text-amber-600">{{ number_format($facture->montant_restant, 2, ',', ' ') }} €</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right: Paiements --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="px-6 pt-5 pb-4 border-b border-slate-100">
                            <h3 class="font-bold text-slate-900">Paiements ({{ $facture->paiements->count() }})</h3>
                        </div>

                        @if ((float) $facture->montant_restant > 0)
                            <form method="POST" action="{{ route('factures.paiements.store', $facture) }}" class="p-6 border-b border-slate-100">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label for="montant" class="block text-xs font-medium text-slate-600 mb-1">Montant (€) <span class="text-red-500">*</span></label>
                                        <input type="number" step="0.01" min="0.01" id="montant" name="montant" value="{{ old('montant') }}" class="block w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">
                                        @error('montant') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="date_paiement" class="block text-xs font-medium text-slate-600 mb-1">Date <span class="text-red-500">*</span></label>
                                        <input type="date" id="date_paiement" name="date_paiement" value="{{ old('date_paiement', now()->toDateString()) }}" class="block w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">
                                        @error('date_paiement') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="mode_paiement" class="block text-xs font-medium text-slate-600 mb-1">Mode <span class="text-red-500">*</span></label>
                                        <select id="mode_paiement" name="mode_paiement" class="block w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">
                                            @foreach (['Virement bancaire', 'Chèque', 'Espèces', 'Carte bancaire'] as $mode)
                                                <option value="{{ $mode }}" {{ old('mode_paiement') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                                            @endforeach
                                        </select>
                                        @error('mode_paiement') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="reference" class="block text-xs font-medium text-slate-600 mb-1">Référence</label>
                                        <input type="text" id="reference" name="reference" value="{{ old('reference') }}" placeholder="Optionnel" class="block w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-4">
                                    <p class="text-xs text-slate-500">Reste dû : <strong class="text-amber-600">{{ number_format($facture->montant_restant, 2, ',', ' ') }} €</strong></p>
                                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">Enregistrer le paiement</button>
                                </div>
                            </form>
                        @endif

                        <div class="p-6 space-y-3">
                            @forelse ($facture->paiements as $paiement)
                                <div class="flex items-center justify-between p-4 border border-slate-100 rounded-xl hover:border-slate-200 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-slate-900">{{ number_format($paiement->montant, 2, ',', ' ') }} €</p>
                                            <p class="text-xs text-slate-500">
                                                {{ $paiement->mode_paiement }}
                                                @if ($paiement->reference)
                                                    · {{ $paiement->reference }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm text-slate-500">{{ $paiement->date_paiement?->format('d/m/Y') }}</span>
                                        <form method="POST" action="{{ route('factures.paiements.destroy', [$facture, $paiement]) }}" onsubmit="return confirm('Supprimer ce paiement ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Supprimer" class="text-slate-400 hover:text-red-500 transition-colors p-1">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-3 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <p class="text-sm">Aucun paiement enregistré pour cette facture.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>