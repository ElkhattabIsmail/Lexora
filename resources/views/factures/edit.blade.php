<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('factures.show', $facture) }}" class="text-slate-400 hover:text-brand-DEFAULT transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <div>
                <p class="text-xs text-slate-500 font-mono">{{ $facture->numero_facture }}</p>
                <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">Modifier la Facture</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <form action="{{ route('factures.update', $facture) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="md:col-span-2">
                            <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Informations de la facture</h3>
                        </div>

                        <div>
                            <label for="client_id" class="block text-sm font-medium text-slate-700">Client <span class="text-red-500">*</span></label>
                            <select id="client_id" name="client_id" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('client_id') border-red-500 @enderror">
                                <option value="">— Sélectionner un client —</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $facture->client_id) == $client->id ? 'selected' : '' }}>{{ $client->nom_complet }}</option>
                                @endforeach
                            </select>
                            @error('client_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="dossier_id" class="block text-sm font-medium text-slate-700">Dossier</label>
                            <select id="dossier_id" name="dossier_id" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">
                                <option value="">— Aucun dossier —</option>
                                @foreach ($dossiers as $dossier)
                                    <option value="{{ $dossier->id }}" {{ old('dossier_id', $facture->dossier_id) == $dossier->id ? 'selected' : '' }}>
                                        {{ $dossier->numero_dossier }} — {{ $dossier->client->nom_complet }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dossier_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="montant" class="block text-sm font-medium text-slate-700">Montant HT (€) <span class="text-red-500">*</span></label>
                            <input type="number" id="montant" name="montant" value="{{ old('montant', $facture->montant) }}" min="0" step="0.01" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('montant') border-red-500 @enderror">
                            @error('montant') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="date_facture" class="block text-sm font-medium text-slate-700">Date de facturation <span class="text-red-500">*</span></label>
                            <input type="date" id="date_facture" name="date_facture" value="{{ old('date_facture', $facture->date_facture?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('date_facture') border-red-500 @enderror">
                            @error('date_facture') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="statut" class="block text-sm font-medium text-slate-700">Statut <span class="text-red-500">*</span></label>
                            <select id="statut" name="statut" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">
                                @foreach (['Non payée', 'Payée'] as $s)
                                    <option value="{{ $s }}" {{ old('statut', $facture->statut) === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-2xl">
                        <a href="{{ route('factures.show', $facture) }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm">Annuler</a>
                        <button type="submit" class="px-6 py-2.5 bg-brand-DEFAULT text-white rounded-lg text-sm font-medium hover:bg-brand-dark transition-colors shadow-sm">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>