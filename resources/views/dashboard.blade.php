<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Tableau de bord') }}
            </h2>
            <div class="flex gap-3">
                <button class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm">
                    Générer Rapport
                </button>
                <button class="px-4 py-2 bg-brand-DEFAULT text-white rounded-lg text-sm font-medium hover:bg-brand-dark transition-colors shadow-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Nouveau Dossier
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- KPI 1 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow cursor-pointer">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-brand-light/10 rounded-full group-hover:scale-110 transition-transform"></div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Dossiers Actifs</p>
                            <h3 class="text-3xl font-bold text-slate-900 mt-1">142</h3>
                        </div>
                        <div class="p-2 bg-brand-50 text-brand-DEFAULT rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                    </div>
                    <div class="flex items-center text-sm">
                        <span class="text-emerald-500 font-medium flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" /></svg>
                            +12%
                        </span>
                        <span class="text-slate-400 ml-2">ce mois-ci</span>
                    </div>
                </div>

                <!-- KPI 2 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow cursor-pointer">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-gold-500/10 rounded-full group-hover:scale-110 transition-transform"></div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Audiences à venir</p>
                            <h3 class="text-3xl font-bold text-slate-900 mt-1">8</h3>
                        </div>
                        <div class="p-2 bg-gold-50 text-gold-600 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                    </div>
                    <div class="flex items-center text-sm">
                        <span class="text-rose-500 font-medium flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            3 demain
                        </span>
                        <span class="text-slate-400 ml-2">cette semaine</span>
                    </div>
                </div>

                <!-- KPI 3 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow cursor-pointer">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full group-hover:scale-110 transition-transform"></div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Revenus mensuels</p>
                            <h3 class="text-3xl font-bold text-slate-900 mt-1">45.2k€</h3>
                        </div>
                        <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                    <div class="flex items-center text-sm">
                        <span class="text-emerald-500 font-medium flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" /></svg>
                            +8.4%
                        </span>
                        <span class="text-slate-400 ml-2">vs le mois dernier</span>
                    </div>
                </div>

                <!-- KPI 4 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow cursor-pointer">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-500/10 rounded-full group-hover:scale-110 transition-transform"></div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Taux de réussite</p>
                            <h3 class="text-3xl font-bold text-slate-900 mt-1">78%</h3>
                        </div>
                        <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                    <div class="flex items-center text-sm w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                        <div class="bg-purple-500 h-1.5 rounded-full" style="width: 78%"></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Chart Area -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-slate-900 font-serif">Évolution des dossiers</h3>
                        <select class="text-sm border-slate-200 rounded-lg bg-slate-50 text-slate-600 focus:ring-brand-DEFAULT focus:border-brand-DEFAULT">
                            <option>Cette année</option>
                            <option>6 derniers mois</option>
                        </select>
                    </div>
                    <!-- Placeholder Chart -->
                    <div class="h-72 w-full flex items-end justify-between gap-2 px-2">
                        <!-- Bars -->
                        <div class="w-1/12 bg-brand-light/20 hover:bg-brand-light/40 transition-colors rounded-t-lg h-[40%] relative group cursor-pointer"><div class="hidden group-hover:block absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-xs py-1 px-2 rounded">Jan</div></div>
                        <div class="w-1/12 bg-brand-light/30 hover:bg-brand-light/50 transition-colors rounded-t-lg h-[55%] relative group cursor-pointer"><div class="hidden group-hover:block absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-xs py-1 px-2 rounded">Fév</div></div>
                        <div class="w-1/12 bg-brand-light/40 hover:bg-brand-light/60 transition-colors rounded-t-lg h-[45%] relative group cursor-pointer"><div class="hidden group-hover:block absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-xs py-1 px-2 rounded">Mar</div></div>
                        <div class="w-1/12 bg-brand-DEFAULT/60 hover:bg-brand-DEFAULT/80 transition-colors rounded-t-lg h-[70%] relative group cursor-pointer"><div class="hidden group-hover:block absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-xs py-1 px-2 rounded">Avr</div></div>
                        <div class="w-1/12 bg-brand-DEFAULT/80 hover:bg-brand-DEFAULT transition-colors rounded-t-lg h-[85%] relative group cursor-pointer"><div class="hidden group-hover:block absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-xs py-1 px-2 rounded">Mai</div></div>
                        <div class="w-1/12 bg-brand-DEFAULT hover:bg-brand-dark transition-colors rounded-t-lg h-[100%] relative group cursor-pointer"><div class="hidden group-hover:block absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-xs py-1 px-2 rounded">Juin</div></div>
                    </div>
                    <div class="flex justify-between mt-2 text-xs text-slate-400 px-2 font-medium">
                        <span>Jan</span>
                        <span>Fév</span>
                        <span>Mar</span>
                        <span>Avr</span>
                        <span>Mai</span>
                        <span>Juin</span>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-0 overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-900 font-serif">Audiences de la semaine</h3>
                        <a href="#" class="text-sm text-brand-DEFAULT hover:text-brand-dark font-medium">Voir tout</a>
                    </div>
                    <div class="flex-1 overflow-y-auto">
                        <ul class="divide-y divide-slate-100">
                            <!-- Item 1 -->
                            <li class="p-4 hover:bg-slate-50 transition-colors cursor-pointer group">
                                <div class="flex gap-4">
                                    <div class="mt-1 bg-gold-50 text-gold-600 rounded-lg p-2 h-10 w-10 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 group-hover:text-brand-DEFAULT transition-colors">Tribunal de Commerce - Affaire Durand</p>
                                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            Demain à 09h00
                                        </p>
                                    </div>
                                </div>
                            </li>
                            <!-- Item 2 -->
                            <li class="p-4 hover:bg-slate-50 transition-colors cursor-pointer group">
                                <div class="flex gap-4">
                                    <div class="mt-1 bg-rose-50 text-rose-600 rounded-lg p-2 h-10 w-10 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 group-hover:text-brand-DEFAULT transition-colors">Cour d'Appel - SARL TechBuild</p>
                                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            Jeu. 12 Nov à 14h30
                                        </p>
                                    </div>
                                </div>
                            </li>
                            <!-- Item 3 -->
                            <li class="p-4 hover:bg-slate-50 transition-colors cursor-pointer group">
                                <div class="flex gap-4">
                                    <div class="mt-1 bg-brand-50 text-brand-DEFAULT rounded-lg p-2 h-10 w-10 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 group-hover:text-brand-DEFAULT transition-colors">Conseil des Prud'hommes - M. Leroy</p>
                                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            Ven. 13 Nov à 10h00
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Clients Table -->
            <div class="mt-6 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-900 font-serif">Dossiers récents</h3>
                    <a href="#" class="text-sm text-brand-DEFAULT hover:text-brand-dark font-medium">Voir tous les dossiers</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 font-medium">N° Dossier</th>
                                <th class="px-6 py-4 font-medium">Client</th>
                                <th class="px-6 py-4 font-medium">Type</th>
                                <th class="px-6 py-4 font-medium">Statut</th>
                                <th class="px-6 py-4 font-medium">Dernière action</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-900">#2026-089</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-DEFAULT flex items-center justify-center font-bold text-xs">JD</div>
                                        <div>
                                            <p class="font-bold text-slate-900">Jean Dupont</p>
                                            <p class="text-xs text-slate-500">Particulier</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">Droit de la famille</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-100">En cours</span>
                                </td>
                                <td class="px-6 py-4 text-slate-500">Aujourd'hui, 10h42</td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-slate-400 hover:text-brand-DEFAULT transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-900">#2026-088</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs">SA</div>
                                        <div>
                                            <p class="font-bold text-slate-900">SARL TechBuild</p>
                                            <p class="text-xs text-slate-500">Entreprise</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">Droit commercial</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-600 border border-amber-100">En attente</span>
                                </td>
                                <td class="px-6 py-4 text-slate-500">Hier, 16h15</td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-slate-400 hover:text-brand-DEFAULT transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
