<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dossiers.show', $dossier) }}" class="text-slate-400 hover:text-brand-DEFAULT transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <div>
                <p class="text-xs text-slate-500 font-mono">{{ $dossier->numero_dossier }}</p>
                <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">Nouvelle Audience</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <form action="{{ route('dossiers.audiences.store', $dossier) }}" method="POST">
                    @csrf
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="md:col-span-2">
                            <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Détails de l'audience</h3>
                        </div>

                        <div>
                            <label for="date" class="block text-sm font-medium text-slate-700">Date <span class="text-red-500">*</span></label>
                            <input type="date" id="date" name="date" value="{{ old('date') }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('date') border-red-500 @enderror">
                            @error('date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="heure" class="block text-sm font-medium text-slate-700">Heure <span class="text-red-500">*</span></label>
                            <input type="time" id="heure" name="heure" value="{{ old('heure') }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('heure') border-red-500 @enderror">
                            @error('heure') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="tribunal" class="block text-sm font-medium text-slate-700">Tribunal <span class="text-red-500">*</span></label>
                            <input type="text" id="tribunal" name="tribunal" value="{{ old('tribunal') }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('tribunal') border-red-500 @enderror" placeholder="Ex: Tribunal de Première Instance de Casablanca">
                            @error('tribunal') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="avocat_id" class="block text-sm font-medium text-slate-700">Avocat responsable <span class="text-red-500">*</span></label>
                            <select id="avocat_id" name="avocat_id" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('avocat_id') border-red-500 @enderror">
                                <option value="">— Sélectionner un avocat —</option>
                                @foreach ($avocats as $avocat)
                                    <option value="{{ $avocat->id }}" {{ old('avocat_id', $dossier->avocat_id) == $avocat->id ? 'selected' : '' }}>
                                        {{ $avocat->nom_complet }}
                                    </option>
                                @endforeach
                            </select>
                            @error('avocat_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="statut" class="block text-sm font-medium text-slate-700">Statut <span class="text-red-500">*</span></label>
                            <select id="statut" name="statut" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">
                                @foreach (['Prévue', 'Annulée', 'Terminée'] as $s)
                                    <option value="{{ $s }}" {{ old('statut', 'Prévue') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="observations" class="block text-sm font-medium text-slate-700">Observations</label>
                            <textarea id="observations" name="observations" rows="4" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20" placeholder="Notes, pièces à présenter...">{{ old('observations') }}</textarea>
                            @error('observations') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    <div class="px-4 sm:px-8 py-5 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 rounded-b-2xl">
                        <a href="{{ route('dossiers.show', $dossier) }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm">Annuler</a>
                        <button type="submit" class="px-6 py-2.5 bg-brand-DEFAULT text-white rounded-lg text-sm font-medium hover:bg-brand-dark transition-colors shadow-sm">Planifier l'audience</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>