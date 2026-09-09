<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Factures') }}
            </h2>
            <a href="{{ route('factures.create') }}" class="px-4 py-2 bg-brand-DEFAULT text-white rounded-lg text-sm font-medium hover:bg-brand-dark transition-colors shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Nouvelle Facture
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

                {{-- Filters --}}
                <div class="p-4 border-b border-slate-100 flex flex-wrap gap-3 items-center">
                    <form method="GET" action="{{ route('factures.index') }}" class="flex flex-wrap gap-3 items-center w-full">
                        <div class="relative w-full lg:w-72">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/></svg>
                            </div>
                            <input type="search" name="search" value="{{ $search }}" class="block w-full p-2.5 pl-10 text-sm text-slate-900 border border-slate-200 rounded-lg bg-slate-50 focus:ring-brand-DEFAULT focus:border-brand-DEFAULT transition-colors" placeholder="N° facture, dossier, client...">
                        </div>
                        <select name="statut" class="text-sm border-slate-200 rounded-lg bg-slate-50 p-2.5 focus:ring-brand-DEFAULT focus:border-brand-DEFAULT">
                            <option value="">Tous les statuts</option>
                            @foreach (['Payée', 'Non payée'] as $s)
                                <option value="{{ $s }}" {{ $statut == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm hover:bg-slate-50 transition-colors shadow-sm">Filtrer</button>
                        <span class="ml-auto text-sm text-slate-500">{{ $factures->total() }} facture(s)</span>
                    </form>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 font-medium">Référence</th>
                                <th class="px-6 py-4 font-medium">Client</th>
                                <th class="px-6 py-4 font-medium">Dossier</th>
                                <th class="px-6 py-4 font-medium">Montant</th>
                                <th class="px-6 py-4 font-medium">Date</th>
                                <th class="px-6 py-4 font-medium">Statut</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($factures as $facture)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-700">{{ $facture->numero_facture }}</td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('clients.show', $facture->client) }}" class="text-brand-DEFAULT hover:underline font-medium">
                                            {{ $facture->client->nom_complet }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">{{ $facture->dossier?->numero_dossier ?? '—' }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-800">{{ number_format($facture->montant, 2, ',', ' ') }} €</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $facture->date_facture?->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">
                                        <x-status-badge :statut="$facture->statut" />
                                    </td>
                                    <td class="px-6 py-4 text-right flex items-center justify-end gap-1">
                                        <a href="{{ route('factures.show', $facture) }}" title="Voir" class="text-slate-400 hover:text-brand-DEFAULT transition-colors p-1">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                        <a href="{{ route('factures.edit', $facture) }}" title="Modifier" class="text-slate-400 hover:text-amber-500 transition-colors p-1">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </a>
                                        <form method="POST" action="{{ route('factures.destroy', $facture) }}" onsubmit="return confirm('Supprimer cette facture ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Supprimer" class="text-slate-400 hover:text-red-500 transition-colors p-1">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center text-slate-400">
                                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <p class="font-medium">Aucune facture trouvée</p>
                                        @if ($search || $statut)
                                            <p class="text-sm mt-1">Essayez d'ajuster vos filtres.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($factures->hasPages())
                    <div class="p-4 border-t border-slate-100">{{ $factures->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>