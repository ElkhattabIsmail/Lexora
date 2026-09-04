<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="/clients" class="text-slate-400 hover:text-brand-DEFAULT transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <h2 class="font-serif font-bold text-2xl text-slate-800 leading-tight">
                    Dossier Client
                </h2>
            </div>
            <div class="flex gap-3">
                <a href="/clients/edit" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    Modifier
                </a>
                <button class="px-4 py-2 bg-brand-DEFAULT text-white rounded-lg text-sm font-medium hover:bg-brand-dark transition-colors shadow-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Nouveau Dossier
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Colonne Gauche: Profil -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Carte principale -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col items-center text-center">
                        <div class="w-24 h-24 rounded-full bg-brand-100 text-brand-DEFAULT flex items-center justify-center font-bold text-3xl mb-4">
                            JD
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 font-serif">Jean Dupont</h3>
                        <span class="px-3 py-1 mt-2 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Particulier</span>
                        
                        <div class="w-full mt-6 space-y-4 text-sm text-left">
                            <div class="flex items-center gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                <span>jean.dupont@email.com</span>
                            </div>
                            <div class="flex items-center gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                <span>06 12 34 56 78</span>
                            </div>
                            <div class="flex items-start gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-slate-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <span>123 Rue de la Justice<br>75001 Paris<br>France</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne Droite: Onglets -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col h-full">
                        <!-- Navigation des onglets -->
                        <div class="border-b border-slate-100 px-6 pt-4 flex gap-6">
                            <button class="pb-3 border-b-2 border-brand-DEFAULT text-brand-DEFAULT font-medium text-sm">
                                Dossiers (2)
                            </button>
                            <button class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 font-medium text-sm transition-colors">
                                Factures (1)
                            </button>
                            <button class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 font-medium text-sm transition-colors">
                                Historique
                            </button>
                        </div>

                        <!-- Contenu (Dossiers) -->
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Dossier 1 -->
                                <div class="p-5 border border-slate-100 rounded-xl hover:border-slate-300 hover:shadow-sm transition-all cursor-pointer group">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center gap-3">
                                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-md text-xs font-bold">#2026-089</span>
                                            <h4 class="font-bold text-slate-900 group-hover:text-brand-DEFAULT transition-colors">Divorce par consentement mutuel</h4>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-100">En cours</span>
                                    </div>
                                    <p class="text-sm text-slate-500 mb-4 line-clamp-2">Procédure de divorce amiable. Rédaction de la convention en cours de finalisation par les deux parties.</p>
                                    <div class="flex gap-4 text-xs text-slate-400">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Prochaine audience: 15 Nov
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            3 documents
                                        </div>
                                    </div>
                                </div>

                                <!-- Dossier 2 -->
                                <div class="p-5 border border-slate-100 rounded-xl hover:border-slate-300 hover:shadow-sm transition-all cursor-pointer group">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center gap-3">
                                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-md text-xs font-bold">#2025-112</span>
                                            <h4 class="font-bold text-slate-900 group-hover:text-brand-DEFAULT transition-colors">Litige immobilier</h4>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">Fermé</span>
                                    </div>
                                    <p class="text-sm text-slate-500 mb-4 line-clamp-2">Conflit de voisinage concernant la limite de propriété. Accord trouvé après médiation.</p>
                                    <div class="flex gap-4 text-xs text-slate-400">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            Clôturé le 12 Fév 2026
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
