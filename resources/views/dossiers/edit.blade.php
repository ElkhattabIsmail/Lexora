<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dossiers.show', $dossier) }}" class="btn-icon hover:text-brand-DEFAULT hover:bg-brand-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">Modifier — {{ $dossier->numero_dossier }}</h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <form action="{{ route('dossiers.update', $dossier) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="md:col-span-2">
                            <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Informations du dossier</h3>
                        </div>

                        <div>
                            <label for="client_id" class="block text-sm font-medium text-slate-700">Client <span class="text-red-500">*</span></label>
                            <select id="client_id" name="client_id" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $dossier->client_id) == $client->id ? 'selected' : '' }}>{{ $client->nom_complet }}</option>
                                @endforeach
                            </select>
                            @error('client_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="avocat_id" class="block text-sm font-medium text-slate-700">Avocat responsable <span class="text-red-500">*</span></label>
                            <select id="avocat_id" name="avocat_id" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">
                                @foreach ($avocats as $avocat)
                                    <option value="{{ $avocat->id }}" {{ old('avocat_id', $dossier->avocat_id) == $avocat->id ? 'selected' : '' }}>{{ $avocat->nom_complet }}</option>
                                @endforeach
                            </select>
                            @error('avocat_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="type_affaire" class="block text-sm font-medium text-slate-700">Type d'affaire <span class="text-red-500">*</span></label>
                            <input type="text" id="type_affaire" name="type_affaire" value="{{ old('type_affaire', $dossier->type_affaire) }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('type_affaire') border-red-500 @enderror">
                            @error('type_affaire') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="statut" class="block text-sm font-medium text-slate-700">Statut <span class="text-red-500">*</span></label>
                            <select id="statut" name="statut" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">
                                @foreach (['En cours', 'Gagné', 'Perdu', 'Fermé'] as $s)
                                    <option value="{{ $s }}" {{ old('statut', $dossier->statut) === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="date_ouverture" class="block text-sm font-medium text-slate-700">Date d'ouverture <span class="text-red-500">*</span></label>
                            <input type="date" id="date_ouverture" name="date_ouverture" value="{{ old('date_ouverture', $dossier->date_ouverture?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('date_ouverture') border-red-500 @enderror">
                            @error('date_ouverture') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="date_fermeture" class="block text-sm font-medium text-slate-700">Date de fermeture</label>
                            <input type="date" id="date_fermeture" name="date_fermeture" value="{{ old('date_fermeture', $dossier->date_fermeture?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('date_fermeture') border-red-500 @enderror">
                            @error('date_fermeture') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2 flex items-center gap-3">
                            <input type="checkbox" id="archive" name="archive" value="1" class="rounded border-slate-300 text-brand-DEFAULT focus:ring-brand-DEFAULT" {{ old('archive', $dossier->archive) ? 'checked' : '' }}>
                            <label for="archive" class="text-sm font-medium text-slate-700">Archiver ce dossier</label>
                        </div>

                    </div>

                    <div class="px-4 sm:px-8 py-5 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 rounded-b-2xl">
                        <a href="{{ route('dossiers.show', $dossier) }}" class="btn-secondary">Annuler</a>
                        <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
