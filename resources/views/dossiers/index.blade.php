<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Dossiers') }}
            </h2>
            <a href="{{ route('dossiers.create') }}" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span class="hidden sm:inline">Nouveau Dossier</span>
                <span class="sm:hidden">Nouveau</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

                {{-- Filters --}}
                <div class="p-4 border-b border-slate-100">
                    <form method="GET" action="{{ route('dossiers.index') }}" class="space-y-3">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/></svg>
                                </div>
                                <input type="search" name="search" value="{{ $search }}" class="block w-full p-2.5 pl-10 text-sm text-slate-900 border border-slate-200 rounded-lg bg-slate-50 focus:ring-brand-DEFAULT focus:border-brand-DEFAULT transition-colors" placeholder="N° dossier, affaire, client...">
                            </div>
                            <select name="statut" class="text-sm border-slate-200 rounded-lg bg-slate-50 p-2.5 focus:ring-brand-DEFAULT focus:border-brand-DEFAULT">
                                <option value="">Tous les statuts</option>
                                @foreach (['En cours', 'Gagné', 'Perdu', 'Fermé'] as $s)
                                    <option value="{{ $s }}" {{ $statut == $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                            <select name="avocat_id" class="text-sm border-slate-200 rounded-lg bg-slate-50 p-2.5 focus:ring-brand-DEFAULT focus:border-brand-DEFAULT">
                                <option value="">Tous les avocats</option>
                                @foreach ($avocats as $avocat)
                                    <option value="{{ $avocat->id }}" {{ $avocatId == $avocat->id ? 'selected' : '' }}>{{ $avocat->nom_complet }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="submit" class="btn-dark">Filtrer</button>
                            @if ($search || $statut || $avocatId)
                                <a href="{{ route('dossiers.index') }}" class="btn-ghost-light">
                                    Réinitialiser
                                </a>
                            @endif
                            <span class="ml-auto text-sm text-slate-500">{{ $dossiers->total() }} dossier(s)</span>
                        </div>
                    </form>
                </div>

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 font-medium">Référence</th>
                                <th class="px-6 py-4 font-medium">Client</th>
                                <th class="px-6 py-4 font-medium">Type d'affaire</th>
                                <th class="px-6 py-4 font-medium">Avocat</th>
                                <th class="px-6 py-4 font-medium">Statut</th>
                                <th class="px-6 py-4 font-medium">Ouvert le</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($dossiers as $dossier)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-700">{{ $dossier->numero_dossier }}</td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('clients.show', $dossier->client) }}" class="text-brand-DEFAULT hover:underline font-medium">
                                            {{ $dossier->client->nom_complet }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">{{ $dossier->type_affaire }}</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $dossier->avocat?->nom_complet ?? '—' }}</td>
                                    <td class="px-6 py-4">
                                        <x-status-badge :statut="$dossier->statut" />
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">{{ $dossier->date_ouverture?->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-right flex items-center justify-end gap-1">
                                        <a href="{{ route('dossiers.show', $dossier) }}" title="Voir" class="btn-icon hover:text-brand-DEFAULT hover:bg-brand-50">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                        <a href="{{ route('dossiers.edit', $dossier) }}" title="Modifier" class="btn-icon hover:text-amber-600 hover:bg-amber-50">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </a>
                                        <form method="POST" action="{{ route('dossiers.destroy', $dossier) }}" onsubmit="return confirm('Supprimer ce dossier ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Supprimer" class="btn-icon-danger">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center text-slate-400">
                                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        <p class="font-medium">Aucun dossier trouvé</p>
                                        @if ($search || $statut || $avocatId)
                                            <p class="text-sm mt-1">Essayez d'ajuster vos filtres.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-slate-100">
                    @forelse ($dossiers as $dossier)
                        <div class="p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded-md text-xs font-bold">{{ $dossier->numero_dossier }}</span>
                                    <h4 class="font-bold text-slate-900 mt-1">{{ $dossier->type_affaire }}</h4>
                                </div>
                                <x-status-badge :statut="$dossier->statut" />
                            </div>
                            <div class="flex items-center gap-3 text-xs text-slate-500 mt-2">
                                <span class="text-brand-DEFAULT font-medium">{{ $dossier->client->nom_complet }}</span>
                                <span>·</span>
                                <span>{{ $dossier->avocat?->nom_complet ?? '—' }}</span>
                                <span>·</span>
                                <span>{{ $dossier->date_ouverture?->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center gap-1 mt-3">
                                <a href="{{ route('dossiers.show', $dossier) }}" class="btn-icon hover:text-brand-DEFAULT hover:bg-brand-50">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                                <a href="{{ route('dossiers.edit', $dossier) }}" class="btn-icon hover:text-amber-600 hover:bg-amber-50">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </a>
                                <form method="POST" action="{{ route('dossiers.destroy', $dossier) }}" onsubmit="return confirm('Supprimer ce dossier ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon-danger">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-16 text-center text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <p class="font-medium">Aucun dossier trouvé</p>
                            @if ($search || $statut || $avocatId)
                                <p class="text-sm mt-1">Essayez d'ajuster vos filtres.</p>
                            @endif
                        </div>
                    @endforelse
                </div>

                @if ($dossiers->hasPages())
                    <div class="p-4 border-t border-slate-100">{{ $dossiers->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
