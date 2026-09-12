<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-serif text-2xl font-bold text-slate-900 leading-tight">
                    Gestion des utilisateurs et des rôles
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Supervisez les comptes du cabinet et attribuez les habilitations d'accès.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                    {{ $users->total() }} utilisateurs au total
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash messages -->
            @if (session('status'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->has('role_id'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $errors->first('role_id') }}</span>
                </div>
            @endif

            <!-- Filtres et recherche -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
                <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                        <div class="sm:col-span-6 md:col-span-5">
                            <label for="search" class="block text-xs font-medium uppercase tracking-wider text-slate-500 mb-1">Recherche</label>
                            <div class="relative">
                                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Nom, prénom ou email..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors" />
                                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="sm:col-span-4 md:col-span-4">
                            <label for="filter_role" class="block text-xs font-medium uppercase tracking-wider text-slate-500 mb-1">Filtrer par rôle</label>
                            <select id="filter_role" name="role_id" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20 transition-colors">
                                <option value="">Tous les rôles</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2 md:col-span-3 flex gap-2">
                            <button type="submit" class="btn-dark w-full">
                                Filtrer
                            </button>
                            @if(request()->hasAny(['search', 'role_id']))
                                <a href="{{ route('admin.users.index') }}" class="btn-ghost-light py-2">
                                    Réinitialiser
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Desktop Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50/75">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Utilisateur</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Contact</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Rôle actuel</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Modifier le rôle</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date d'inscription</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($users as $u)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-sm text-slate-700">
                                                {{ mb_substr($u->prenom, 0, 1) }}{{ mb_substr($u->nom, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-slate-900">{{ $u->nom_complet }}</div>
                                                <div class="text-xs text-slate-500">ID #{{ $u->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900">{{ $u->email }}</div>
                                        <div class="text-xs text-slate-500">{{ $u->telephone ?? 'Non renseigné' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($u->isAdministrateur())
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500 mr-1.5"></span>
                                                Administrateur
                                            </span>
                                        @elseif($u->isAvocat())
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                                                Avocat
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                Assistant Juridique
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form method="POST" action="{{ route('admin.users.update-role', $u) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role_id" class="text-xs py-1.5 px-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20">
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}" {{ $u->role_id === $role->id ? 'selected' : '' }}>
                                                        {{ $role->nom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn-dark px-3 py-1.5 text-xs">
                                                Changer
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        {{ $u->created_at ? $u->created_at->format('d/m/Y H:i') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">
                                        Aucun utilisateur trouvé correspondant aux critères de recherche.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-slate-100">
                    @forelse ($users as $u)
                        <div class="p-4 hover:bg-slate-50/50 transition-colors">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-sm text-slate-700 shrink-0">
                                    {{ mb_substr($u->prenom, 0, 1) }}{{ mb_substr($u->nom, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-slate-900 truncate">{{ $u->nom_complet }}</div>
                                    <div class="text-xs text-slate-500 truncate">{{ $u->email }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 mb-3">
                                @if($u->isAdministrateur())
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                        Administrateur
                                    </span>
                                @elseif($u->isAvocat())
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                        Avocat
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Assistant Juridique
                                    </span>
                                @endif
                                <span class="text-xs text-slate-400">{{ $u->created_at ? $u->created_at->format('d/m/Y') : '-' }}</span>
                            </div>
                            <form method="POST" action="{{ route('admin.users.update-role', $u) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="role_id" class="flex-1 text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg focus:border-brand-DEFAULT focus:ring-brand-DEFAULT/20">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" {{ $u->role_id === $role->id ? 'selected' : '' }}>
                                            {{ $role->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn-dark px-4 py-2 text-xs">
                                    Changer
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-slate-400 text-sm">
                            Aucun utilisateur trouvé correspondant aux critères de recherche.
                        </div>
                    @endforelse
                </div>

                @if ($users->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
