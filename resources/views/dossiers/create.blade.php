<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dossiers.index') }}" class="btn-icon hover:text-brand-DEFAULT hover:bg-brand-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">Nouveau Dossier</h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <form action="{{ route('dossiers.store') }}" method="POST">
                    @csrf
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="md:col-span-2">
                            <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Informations du dossier</h3>
                        </div>

                        {{-- Searchable Client dropdown --}}
                        <div x-data="searchableSelect({
                                options: [
                                    @foreach ($clients as $client)
                                    { value: '{{ $client->id }}', label: '{{ addslashes($client->nom_complet) }}' },
                                    @endforeach
                                ],
                                selected: '{{ old('client_id', request('client_id')) }}',
                                placeholder: '— Sélectionner un client —'
                            })" class="relative">
                            <label class="block text-sm font-medium text-slate-700">Client <span class="text-red-500">*</span></label>
                            <input type="hidden" name="client_id" :value="selectedValue">
                            <div class="mt-1 relative">
                                <input
                                    type="text"
                                    id="client_id"
                                    x-model="query"
                                    @focus="open = true"
                                    @blur="handleBlur()"
                                    @input="open = true"
                                    :placeholder="selectedLabel || placeholder"
                                    :class="selectedLabel ? 'text-slate-900' : 'text-slate-400'"
                                    class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 pr-8 @error('client_id') border-red-500 @enderror"
                                    autocomplete="off"
                                >
                                <span class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-slate-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </span>
                            </div>
                            <ul
                                x-show="open && filtered.length > 0"
                                x-transition
                                class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-lg max-h-52 overflow-y-auto"
                            >
                                <template x-for="opt in filtered" :key="opt.value">
                                    <li
                                        @mousedown.prevent="select(opt)"
                                        class="px-4 py-2 text-sm text-slate-700 cursor-pointer hover:bg-brand-50 hover:text-brand-DEFAULT"
                                        x-text="opt.label"
                                    ></li>
                                </template>
                            </ul>
                            <p x-show="open && filtered.length === 0 && query.length > 0" class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow px-4 py-2 text-sm text-slate-400">Aucun résultat</p>
                            @error('client_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Searchable Avocat dropdown --}}
                        <div x-data="searchableSelect({
                                options: [
                                    @foreach ($avocats as $avocat)
                                    { value: '{{ $avocat->id }}', label: '{{ addslashes($avocat->nom_complet) }}' },
                                    @endforeach
                                ],
                                selected: '{{ old('avocat_id') }}',
                                placeholder: '— Sélectionner un avocat —'
                            })" class="relative">
                            <label class="block text-sm font-medium text-slate-700">Avocat responsable <span class="text-red-500">*</span></label>
                            <input type="hidden" name="avocat_id" :value="selectedValue">
                            <div class="mt-1 relative">
                                <input
                                    type="text"
                                    id="avocat_id"
                                    x-model="query"
                                    @focus="open = true"
                                    @blur="handleBlur()"
                                    @input="open = true"
                                    :placeholder="selectedLabel || placeholder"
                                    :class="selectedLabel ? 'text-slate-900' : 'text-slate-400'"
                                    class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 pr-8 @error('avocat_id') border-red-500 @enderror"
                                    autocomplete="off"
                                >
                                <span class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-slate-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </span>
                            </div>
                            <ul
                                x-show="open && filtered.length > 0"
                                x-transition
                                class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-lg max-h-52 overflow-y-auto"
                            >
                                <template x-for="opt in filtered" :key="opt.value">
                                    <li
                                        @mousedown.prevent="select(opt)"
                                        class="px-4 py-2 text-sm text-slate-700 cursor-pointer hover:bg-brand-50 hover:text-brand-DEFAULT"
                                        x-text="opt.label"
                                    ></li>
                                </template>
                            </ul>
                            <p x-show="open && filtered.length === 0 && query.length > 0" class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow px-4 py-2 text-sm text-slate-400">Aucun résultat</p>
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

                    <div class="px-4 sm:px-8 py-5 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 rounded-b-2xl">
                        <a href="{{ route('dossiers.index') }}" class="btn-secondary">Annuler</a>
                        <button type="submit" class="btn-primary">Créer le dossier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
