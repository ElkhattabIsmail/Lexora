<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dossiers.index') }}" class="text-slate-400 hover:text-brand-DEFAULT transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">Nouveau Dossier</h2>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <form action="{{ route('dossiers.store') }}" method="POST">
                    @csrf
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="md:col-span-2">
                            <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Informations du dossier</h3>
                        </div>

                        <div>
                            <label for="client_id" class="block text-sm font-medium text-slate-700">Client <span class="text-red-500">*</span></label>
                            <select id="client_id" name="client_id" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('client_id') border-red-500 @enderror">
                                <option value="">— Sélectionner un client —</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', request('client_id')) == $client->id ? 'selected' : '' }}>
                                        {{ $client->nom_complet }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="avocat_id" class="block text-sm font-medium text-slate-700">Avocat responsable <span class="text-red-500">*</span></label>
                            <select id="avocat_id" name="avocat_id" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('avocat_id') border-red-500 @enderror">
                                <option value="">— Sélectionner un avocat —</option>
                                @foreach ($avocats as $avocat)
                                    <option value="{{ $avocat->id }}" {{ old('avocat_id') == $avocat->id ? 'selected' : '' }}>
                                        {{ $avocat->nom_complet }}
                                    </option>
                                @endforeach
                            </select>
                            @error('avocat_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="type_affaire" class="block text-sm font-medium text-slate-700">Type d'affaire <span class="text-red-500">*</span></label>
                            <input type="text" id="type_affaire" name="type_affaire" value="{{ old('type_affaire') }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('type_affaire') border-red-500 @enderror" placeholder="Ex: Divorce, Litige immobilier...">
                            @error('type_affaire') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="statut" class="block text-sm font-medium text-slate-700">Statut <span class="text-red-500">*</span></label>
                            <select id="statut" name="statut" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">
                                @foreach (['En cours', 'Gagné', 'Perdu', 'Fermé'] as $s)
                                    <option value="{{ $s }}" {{ old('statut', 'En cours') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="date_ouverture" class="block text-sm font-medium text-slate-700">Date d'ouverture <span class="text-red-500">*</span></label>
                            <input type="date" id="date_ouverture" name="date_ouverture" value="{{ old('date_ouverture', now()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('date_ouverture') border-red-500 @enderror">
                            @error('date_ouverture') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="date_fermeture" class="block text-sm font-medium text-slate-700">Date de fermeture</label>
                            <input type="date" id="date_fermeture" name="date_fermeture" value="{{ old('date_fermeture') }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('date_fermeture') border-red-500 @enderror">
                            @error('date_fermeture') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-2xl">
                        <a href="{{ route('dossiers.index') }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm">Annuler</a>
                        <button type="submit" class="px-6 py-2.5 bg-brand-DEFAULT text-white rounded-lg text-sm font-medium hover:bg-brand-dark transition-colors shadow-sm">Créer le dossier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
