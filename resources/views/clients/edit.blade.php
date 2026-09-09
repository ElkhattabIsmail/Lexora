<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('clients.show', $client) }}" class="text-slate-400 hover:text-brand-DEFAULT transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">
                Modifier — {{ $client->nom_complet }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <form action="{{ route('clients.update', $client) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            {{-- Type Client --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Type de client</label>
                                <div class="flex gap-4">
                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm w-full md:w-64 {{ old('type', $client->type) === 'Particulier' ? 'border-brand-DEFAULT ring-1 ring-brand-DEFAULT' : 'border-slate-300 hover:border-slate-400' }} transition-colors">
                                        <input type="radio" name="type" value="Particulier" class="sr-only" {{ old('type', $client->type) === 'Particulier' ? 'checked' : '' }}>
                                        <span class="flex flex-1 flex-col"><span class="block text-sm font-medium text-slate-900">Particulier</span></span>
                                        @if (old('type', $client->type) === 'Particulier')
                                            <svg class="h-5 w-5 text-brand-DEFAULT" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                                        @endif
                                    </label>
                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm w-full md:w-64 {{ old('type', $client->type) === 'Entreprise' ? 'border-brand-DEFAULT ring-1 ring-brand-DEFAULT' : 'border-slate-300 hover:border-slate-400' }} transition-colors">
                                        <input type="radio" name="type" value="Entreprise" class="sr-only" {{ old('type', $client->type) === 'Entreprise' ? 'checked' : '' }}>
                                        <span class="flex flex-1 flex-col"><span class="block text-sm font-medium text-slate-900">Entreprise</span></span>
                                        @if (old('type', $client->type) === 'Entreprise')
                                            <svg class="h-5 w-5 text-brand-DEFAULT" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                                        @endif
                                    </label>
                                </div>
                                @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2 mt-4">
                                <h3 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2">Informations Générales</h3>
                            </div>

                            <div>
                                <label for="nom" class="block text-sm font-medium text-slate-700">Nom <span class="text-red-500">*</span></label>
                                <input type="text" id="nom" name="nom" value="{{ old('nom', $client->nom) }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('nom') border-red-500 @enderror">
                                @error('nom') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="prenom" class="block text-sm font-medium text-slate-700">Prénom</label>
                                <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $client->prenom) }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">
                            </div>

                            <div class="md:col-span-2 mt-4">
                                <h3 class="text-lg font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2">Coordonnées</h3>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $client->email) }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 @error('email') border-red-500 @enderror">
                                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="telephone" class="block text-sm font-medium text-slate-700">Téléphone</label>
                                <input type="tel" id="telephone" name="telephone" value="{{ old('telephone', $client->telephone) }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">
                            </div>

                            <div class="md:col-span-2">
                                <label for="adresse" class="block text-sm font-medium text-slate-700">Adresse postale</label>
                                <textarea id="adresse" name="adresse" rows="3" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">{{ old('adresse', $client->adresse) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-2xl">
                        <a href="{{ route('clients.show', $client) }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm">
                            Annuler
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-brand-DEFAULT text-white rounded-lg text-sm font-medium hover:bg-brand-dark transition-colors shadow-sm">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
