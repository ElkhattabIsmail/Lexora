<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Clients') }}
            </h2>
            <a href="/clients/create" class="px-4 py-2 bg-brand-DEFAULT text-white rounded-lg text-sm font-medium hover:bg-brand-dark transition-colors shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Nouveau Client
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
                
                <!-- Filters and Search -->
                <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex gap-2 w-full md:w-auto">
                        <div class="relative w-full md:w-72">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/></svg>
                            </div>
                            <input type="search" class="block w-full p-2.5 pl-10 text-sm text-slate-900 border border-slate-200 rounded-lg bg-slate-50 focus:ring-brand-DEFAULT focus:border-brand-DEFAULT transition-colors" placeholder="Rechercher un client..." required>
                        </div>
                        <button class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm hover:bg-slate-50 transition-colors shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                            Filtres
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 font-medium w-12">
                                    <input type="checkbox" class="rounded border-slate-300 text-brand-DEFAULT focus:ring-brand-DEFAULT">
                                </th>
                                <th class="px-6 py-4 font-medium">Nom complet</th>
                                <th class="px-6 py-4 font-medium">Type</th>
                                <th class="px-6 py-4 font-medium">Téléphone</th>
                                <th class="px-6 py-4 font-medium">Dossiers</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="rounded border-slate-300 text-brand-DEFAULT focus:ring-brand-DEFAULT">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-DEFAULT flex items-center justify-center font-bold">JD</div>
                                        <div>
                                            <a href="/clients/show" class="font-bold text-slate-900 hover:text-brand-DEFAULT transition-colors">Jean Dupont</a>
                                            <p class="text-xs text-slate-500">jean.dupont@email.com</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Particulier</span>
                                </td>
                                <td class="px-6 py-4 text-slate-500">06 12 34 56 78</td>
                                <td class="px-6 py-4 font-medium text-slate-700">2 en cours</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="/clients/show" class="text-slate-400 hover:text-brand-DEFAULT transition-colors mx-1">
                                        <svg class="w-5 h-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="rounded border-slate-300 text-brand-DEFAULT focus:ring-brand-DEFAULT">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gold-100 text-gold-600 flex items-center justify-center font-bold">SA</div>
                                        <div>
                                            <a href="/clients/show" class="font-bold text-slate-900 hover:text-brand-DEFAULT transition-colors">SARL TechBuild</a>
                                            <p class="text-xs text-slate-500">contact@techbuild.fr</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-brand-50 text-brand-DEFAULT">Entreprise</span>
                                </td>
                                <td class="px-6 py-4 text-slate-500">01 45 67 89 00</td>
                                <td class="px-6 py-4 font-medium text-slate-700">5 archivés</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="/clients/show" class="text-slate-400 hover:text-brand-DEFAULT transition-colors mx-1">
                                        <svg class="w-5 h-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Placeholder -->
                <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-sm text-slate-500">Affichage de 1 à 10 sur 42 clients</span>
                    <div class="flex gap-1">
                        <button class="px-3 py-1 border border-slate-200 rounded text-slate-400 cursor-not-allowed text-sm">Précédent</button>
                        <button class="px-3 py-1 bg-brand-DEFAULT text-white rounded text-sm">1</button>
                        <button class="px-3 py-1 border border-slate-200 hover:bg-slate-50 rounded text-slate-700 text-sm transition-colors">2</button>
                        <button class="px-3 py-1 border border-slate-200 hover:bg-slate-50 rounded text-slate-700 text-sm transition-colors">3</button>
                        <button class="px-3 py-1 border border-slate-200 hover:bg-slate-50 rounded text-slate-700 text-sm transition-colors">Suivant</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
