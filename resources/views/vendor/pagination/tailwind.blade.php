@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination des résultats">

        <div class="flex gap-2 items-center justify-between sm:hidden">

            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-300 cursor-not-allowed leading-5 rounded-md">
                    Précédent
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-800 bg-white border border-slate-300 leading-5 rounded-md hover:text-slate-700 focus:outline-none focus:ring ring-slate-300 focus:border-brand-light active:bg-slate-100 active:text-slate-800 transition ease-in-out duration-150 hover:bg-slate-100">
                    Précédent
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-800 bg-white border border-slate-300 leading-5 rounded-md hover:text-slate-700 focus:outline-none focus:ring ring-slate-300 focus:border-brand-light active:bg-slate-100 active:text-slate-800 transition ease-in-out duration-150 hover:bg-slate-100">
                    Suivant
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-300 cursor-not-allowed leading-5 rounded-md">
                    Suivant
                </span>
            @endif

        </div>

        <div class="hidden sm:flex-1 sm:flex sm:gap-2 sm:items-center sm:justify-between">

            <div>
                <p class="text-sm text-slate-700 leading-5">
                    Affichage de
                    @if ($paginator->firstItem())
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        à
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    sur
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    résultats
                </p>
            </div>

            <div>
                <span class="inline-flex rtl:flex-row-reverse shadow-sm rounded-md">

                    {{-- Lien vers la page précédente --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Page précédente">
                            <span class="inline-flex items-center px-2 py-2 text-sm font-medium text-slate-500 bg-white border border-slate-300 cursor-not-allowed rounded-l-md leading-5" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-2 py-2 text-sm font-medium text-slate-500 bg-white border border-slate-300 rounded-l-md leading-5 hover:text-slate-400 focus:outline-none focus:ring ring-slate-300 focus:border-brand-light active:bg-slate-100 active:text-slate-500 transition ease-in-out duration-150" aria-label="Page précédente">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Éléments de pagination --}}
                    @foreach ($elements as $element)
                        {{-- Séparateur "trois points" --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-slate-700 bg-white border border-slate-300 cursor-default leading-5">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Tableau de liens --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-slate-700 bg-slate-200 border border-slate-300 cursor-default leading-5">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-slate-700 bg-white border border-slate-300 leading-5 hover:text-slate-700 focus:outline-none focus:ring ring-slate-300 focus:border-brand-light active:bg-slate-100 active:text-slate-700 transition ease-in-out duration-150 hover:bg-slate-100" aria-label="Aller à la page {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Lien vers la page suivante --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-slate-500 bg-white border border-slate-300 rounded-r-md leading-5 hover:text-slate-400 focus:outline-none focus:ring ring-slate-300 focus:border-brand-light active:bg-slate-100 active:text-slate-500 transition ease-in-out duration-150" aria-label="Page suivante">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Page suivante">
                            <span class="inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-slate-500 bg-white border border-slate-300 cursor-not-allowed rounded-r-md leading-5" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif