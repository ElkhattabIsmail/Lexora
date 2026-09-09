<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Clients') }}
            </h2>
            <a href="{{ route('clients.create') }}" class="px-4 py-2 bg-brand-DEFAULT text-white rounded-lg text-sm font-medium hover:bg-brand-dark transition-colors shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Nouveau Client
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">

                {{-- Search --}}
                <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                    <form method="GET" action="{{ route('clients.index') }}" class="flex gap-2 w-full md:w-auto">
                        <div class="relative w-full md:w-72">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/></svg>
                            </div>
                            <input type="search" name="search" value="{{ $search }}" class="block w-full p-2.5 pl-10 text-sm text-slate-900 border border-slate-200 rounded-lg bg-slate-50 focus:ring-brand-DEFAULT focus:border-brand-DEFAULT transition-colors" placeholder="Rechercher un client...">
                        </div>
                        <button type="submit" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm hover:bg-slate-50 transition-colors shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                            Filtrer
                        </button>
                        <select name="type" class="text-sm border-slate-200 rounded-lg bg-slate-50 py-2.5 px-3 focus:ring-brand-DEFAULT focus:border-brand-DEFAULT">
                            <option value="">Tous les types</option>
                            <option value="Particulier" {{ $type === 'Particulier' ? 'selected' : '' }}>Particulier</option>
                            <option value="Entreprise" {{ $type === 'Entreprise' ? 'selected' : '' }}>Entreprise</option>
                        </select>
                    </form>
                    <p class="text-sm text-slate-500 shrink-0">{{ $clients->total() }} client(s)</p>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 font-medium">Nom complet</th>
                                <th class="px-6 py-4 font-medium">Type</th>
                                <th class="px-6 py-4 font-medium">Téléphone</th>
                                <th class="px-6 py-4 font-medium">Dossiers</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($clients as $client)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full {{ $client->isEntreprise() ? 'bg-gold-100 text-gold-600' : 'bg-brand-100 text-brand-DEFAULT' }} flex items-center justify-center font-bold text-sm shrink-0">
                                                {{ strtoupper(mb_substr($client->nom, 0, 1)) }}{{ $client->prenom ? strtoupper(mb_substr($client->prenom, 0, 1)) : '' }}
                                            </div>
                                            <div>
                                                <a href="{{ route('clients.show', $client) }}" class="font-bold text-slate-900 hover:text-brand-DEFAULT transition-colors">
                                                    {{ $client->nom_complet }}
                                                </a>
                                                @if ($client->email)
                                                    <p class="text-xs text-slate-500">{{ $client->email }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($client->isEntreprise())
                                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-brand-50 text-brand-DEFAULT">Entreprise</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Particulier</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">{{ $client->telephone ?? '—' }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-700">{{ $client->dossiers_count }}</td>
                                    <td class="px-6 py-4 text-right flex items-center justify-end gap-1">
                                        <a href="{{ route('clients.show', $client) }}" title="Voir" class="text-slate-400 hover:text-brand-DEFAULT transition-colors p-1">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                        <a href="{{ route('clients.edit', $client) }}" title="Modifier" class="text-slate-400 hover:text-amber-500 transition-colors p-1">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </a>
                                        <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Supprimer ce client ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Supprimer" class="text-slate-400 hover:text-red-500 transition-colors p-1">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center text-slate-400">
                                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        <p class="font-medium">Aucun client trouvé</p>
                                        @if ($search || $type)
                                            <p class="text-sm mt-1">Essayez un autre terme de recherche.</p>
                                        @else
                                            <a href="{{ route('clients.create') }}" class="mt-3 inline-block text-brand-DEFAULT text-sm hover:underline">Créer le premier client</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($clients->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $clients->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
