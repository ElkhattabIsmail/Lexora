<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('dossiers.index') }}" class="text-slate-400 hover:text-brand-DEFAULT transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <p class="text-xs text-slate-500 font-mono">{{ $dossier->numero_dossier }}</p>
                    <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">{{ $dossier->type_affaire }}</h2>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('dossiers.edit', $dossier) }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    Modifier
                </a>
                <a href="{{ route('dossiers.audiences.create', $dossier) }}" class="px-4 py-2 bg-brand-DEFAULT text-white rounded-lg text-sm font-medium hover:bg-brand-dark transition-colors shadow-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Nouvelle Audience
                </a>
            </div>
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
                            <x-status-badge :statut="$dossier->statut" />
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Client</span>
                            <a href="{{ route('clients.show', $dossier->client) }}" class="font-medium text-brand-DEFAULT hover:underline">{{ $dossier->client->nom_complet }}</a>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Avocat</span>
                            <span class="font-medium text-slate-700">{{ $dossier->avocat?->nom_complet ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Ouverture</span>
                            <span class="text-slate-700">{{ $dossier->date_ouverture?->format('d/m/Y') }}</span>
                        </div>
                        @if ($dossier->date_fermeture)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">Fermeture</span>
                                <span class="text-slate-700">{{ $dossier->date_fermeture->format('d/m/Y') }}</span>
                            </div>
                        @endif
                        @if ($dossier->archive)
                            <div class="mt-3 px-3 py-2 bg-amber-50 border border-amber-100 text-amber-700 rounded-lg text-xs font-medium text-center">
                                Dossier archivé
                            </div>
                        @endif
                    </div>

                    {{-- Document Upload --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                        <h3 class="font-bold text-slate-900 mb-4">Téléverser un document</h3>
                        <form action="{{ route('dossiers.documents.store', $dossier) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-3">
                                <div>
                                    <label for="nom_doc" class="block text-xs font-medium text-slate-600 mb-1">Nom du document</label>
                                    <input type="text" id="nom_doc" name="nom" placeholder="Optionnel" class="block w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">
                                </div>
                                <div>
                                    <label for="type_doc" class="block text-xs font-medium text-slate-600 mb-1">Type de document</label>
                                    <select id="type_doc" name="type" class="block w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20">
                                        @foreach (['Autre', 'Contrat', 'Plaidoirie', 'Jugement', 'Preuve'] as $docType)
                                            <option value="{{ $docType }}" {{ old('type', 'Autre') === $docType ? 'selected' : '' }}>{{ $docType }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="fichier" class="block text-xs font-medium text-slate-600 mb-1">Fichier <span class="text-red-500">*</span></label>
                                    <input type="file" id="fichier" name="fichier" class="block w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-brand-50 file:text-brand-DEFAULT hover:file:bg-brand-100 transition-colors">
                                    @error('fichier') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <button type="submit" class="w-full py-2 bg-brand-DEFAULT text-white rounded-lg text-sm font-medium hover:bg-brand-dark transition-colors">Téléverser</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Right: Content --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Audiences --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="px-6 pt-5 pb-4 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="font-bold text-slate-900">Audiences ({{ $dossier->audiences->count() }})</h3>
                            <a href="{{ route('dossiers.audiences.create', $dossier) }}" class="text-brand-DEFAULT text-sm hover:underline flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Ajouter
                            </a>
                        </div>
                        <div class="p-6 space-y-3">
                            @forelse ($dossier->audiences as $audience)
                                <div class="flex items-center justify-between p-4 border border-slate-100 rounded-xl hover:border-slate-200 transition-colors">
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $audience->tribunal }}</p>
                                        <p class="text-sm text-slate-500">{{ $audience->date->format('d/m/Y') }} à {{ $audience->heure }}</p>
                                        @if ($audience->observations)
                                            <p class="text-xs text-slate-400 mt-1 line-clamp-1">{{ $audience->observations }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <x-status-badge :statut="$audience->statut" />
                                        <a href="{{ route('dossiers.audiences.edit', [$dossier, $audience]) }}" class="text-slate-400 hover:text-amber-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </a>
                                        <form method="POST" action="{{ route('dossiers.audiences.destroy', [$dossier, $audience]) }}" onsubmit="return confirm('Supprimer cette audience ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400 text-center py-4">Aucune audience planifiée.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Documents --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="px-6 pt-5 pb-4 border-b border-slate-100">
                            <h3 class="font-bold text-slate-900">Documents ({{ $dossier->documents->count() }})</h3>
                        </div>
                        <div class="p-6 space-y-2">
                            @forelse ($dossier->documents as $document)
                                <div class="flex items-center justify-between p-3 border border-slate-100 rounded-lg hover:border-slate-200 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">{{ $document->nom }}</p>
                                            <p class="text-xs text-slate-400">{{ $document->taille_formattee }} · {{ $document->uploader?->nom_complet }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ Storage::url($document->chemin) }}" target="_blank" class="text-slate-400 hover:text-brand-DEFAULT transition-colors p-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        </a>
                                        <form method="POST" action="{{ route('dossiers.documents.destroy', [$dossier, $document]) }}" onsubmit="return confirm('Supprimer ce document ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors p-1">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400 text-center py-4">Aucun document dans ce dossier.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Historique --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="px-6 pt-5 pb-4 border-b border-slate-100">
                            <h3 class="font-bold text-slate-900">Historique ({{ $dossier->historiques->count() }})</h3>
                        </div>
                        <div class="p-6 space-y-3 max-h-80 overflow-y-auto">
                            @forelse ($dossier->historiques->sortByDesc('date_action') as $historique)
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-brand-50 text-brand-DEFAULT rounded-lg flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-slate-800">{{ $historique->action }}</p>
                                        <p class="text-xs text-slate-400">
                                            {{ $historique->date_action?->format('d/m/Y H:i') }} · {{ $historique->user?->nom_complet ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400 text-center py-4">Aucune action enregistrée.</p>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
