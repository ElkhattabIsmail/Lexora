<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Tableau de bord') }}
            </h2>
            <a href="{{ route('dossiers.create') }}" class="px-4 py-2 bg-brand-DEFAULT text-white rounded-lg text-sm font-medium hover:bg-brand-dark transition-colors shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Nouveau Dossier
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- KPI 1 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-brand-light/10 rounded-full group-hover:scale-110 transition-transform"></div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Dossiers Actifs</p>
                            <h3 class="text-3xl font-bold text-slate-900 mt-1">{{ number_format($stats['dossiers_actifs']) }}</h3>
                        </div>
                        <div class="p-2 bg-brand-50 text-brand-DEFAULT rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                    </div>
                    <div class="text-sm text-slate-400">
                        <a href="{{ route('dossiers.index') }}" class="text-brand-DEFAULT font-medium hover:underline">Voir tous les dossiers</a>
                    </div>
                </div>

                <!-- KPI 2 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-gold-500/10 rounded-full group-hover:scale-110 transition-transform"></div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Audiences à venir</p>
                            <h3 class="text-3xl font-bold text-slate-900 mt-1">{{ number_format($stats['audiences_a_venir']) }}</h3>
                        </div>
                        <div class="p-2 bg-gold-50 text-gold-600 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                    </div>
                    <div class="text-sm text-slate-400">
                        <span class="text-rose-500 font-medium flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            à venir
                        </span>
                        <span class="text-slate-400 ml-2">planifiées</span>
                    </div>
                </div>

                <!-- KPI 3 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full group-hover:scale-110 transition-transform"></div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Revenus du mois</p>
                            <h3 class="text-3xl font-bold text-slate-900 mt-1">{{ number_format($stats['revenus_du_mois'], 2, ',', ' ') }} €</h3>
                        </div>
                        <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                    <div class="text-sm text-slate-400">
                        <span class="text-slate-500">factures encaissées ce mois-ci</span>
                    </div>
                </div>

                <!-- KPI 4 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-500/10 rounded-full group-hover:scale-110 transition-transform"></div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Taux de réussite</p>
                            <h3 class="text-3xl font-bold text-slate-900 mt-1">{{ $stats['taux_reussite'] }}%</h3>
                        </div>
                        <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                    <div class="text-sm text-slate-400 mb-2">dossiers gagnés</div>
                    <div class="text-sm text-slate-400">Total clients : {{ number_format($stats['total_clients']) }}</div>
                    <div class="mt-auto w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-purple-500 h-1.5 rounded-full" @style(['width' => $stats['taux_reussite'] . '%'])></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Chart Area -->
                @php
                    $moisLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
                    $maxMois = max($stats['dossiers_par_mois']->max() ?? 0, 1);
                @endphp
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-slate-900 font-serif">Évolution des dossiers ({{ now()->year }})</h3>
                        <span class="text-sm text-slate-400">{{ $stats['dossiers_par_mois']->sum() }} dossier(s) ouverts cette année</span>
                    </div>
                    <div class="h-72 w-full flex items-end justify-between gap-2 px-2">
                        @foreach ($moisLabels as $index => $label)
                            @php
                                $count = $stats['dossiers_par_mois']->get($index + 1, 0);
                                $height = $count > 0 ? max(8, (int) round(($count / $maxMois) * 100)) : 4;
                            @endphp
                            <div class="w-1/12 {{ $count > 0 ? 'bg-brand-DEFAULT hover:bg-brand-dark' : 'bg-brand-light/20' }} transition-colors rounded-t-lg relative group cursor-pointer"   @style(['height' => $height . '%'])>
                                <div class="hidden group-hover:block absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-xs py-1 px-2 rounded whitespace-nowrap">{{ $label }} — {{ $count }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between mt-2 text-xs text-slate-400 px-2 font-medium">
                        @foreach ($moisLabels as $label)
                            <span>{{ $label }}</span>
                        @endforeach
                    </div>
                </div>

                <!-- Upcoming Audiences -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-0 overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-900 font-serif">Prochaines audiences</h3>
                        <a href="{{ route('dossiers.index') }}" class="text-sm text-brand-DEFAULT hover:text-brand-dark font-medium">Voir tout</a>
                    </div>
                    <div class="flex-1 overflow-y-auto">
                        <ul class="divide-y divide-slate-100">
                            @forelse ($stats['prochaines_audiences'] as $audience)
                                <li class="p-4 hover:bg-slate-50 transition-colors cursor-pointer group">
                                    <a href="{{ route('dossiers.show', $audience->dossier) }}" class="flex gap-4">
                                        <div class="mt-1 bg-gold-50 text-gold-600 rounded-lg p-2 h-10 w-10 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" /></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 group-hover:text-brand-DEFAULT transition-colors">{{ $audience->tribunal }}</p>
                                            <p class="text-sm text-slate-500 mt-0.5">{{ $audience->dossier->client->nom_complet }}</p>
                                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                {{ $audience->date->format('d M Y') }} à {{ substr($audience->heure, 0, 5) }}
                                            </p>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="p-8 text-center text-sm text-slate-400">Aucune audience planifiée.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Dossiers Table -->
            <div class="mt-6 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-900 font-serif">Dossiers récents</h3>
                    <a href="{{ route('dossiers.index') }}" class="text-sm text-brand-DEFAULT hover:text-brand-dark font-medium">Voir tous les dossiers</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 font-medium">N° Dossier</th>
                                <th class="px-6 py-4 font-medium">Client</th>
                                <th class="px-6 py-4 font-medium">Type</th>
                                <th class="px-6 py-4 font-medium">Statut</th>
                                <th class="px-6 py-4 font-medium">Ouvert le</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($stats['derniers_dossiers'] as $dossier)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-900">{{ $dossier->numero_dossier }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full {{ $dossier->client->isEntreprise() ? 'bg-gold-100 text-gold-600' : 'bg-brand-100 text-brand-DEFAULT' }} flex items-center justify-center font-bold text-xs">
                                                {{ strtoupper(mb_substr($dossier->client->nom, 0, 1)) }}
                                            </div>
                                            <p class="font-bold text-slate-900">{{ $dossier->client->nom_complet }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">{{ $dossier->type_affaire }}</td>
                                    <td class="px-6 py-4">
                                            <x-status-badge :statut="$dossier->statut" />
                                        </td>
                                    <td class="px-6 py-4 text-slate-500">{{ $dossier->date_ouverture?->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('dossiers.show', $dossier) }}" class="inline-block text-slate-400 hover:text-brand-DEFAULT transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">Aucun dossier enregistré pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>