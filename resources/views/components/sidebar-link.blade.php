@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold bg-brand-DEFAULT/20 text-gold-400 focus:outline-none focus:bg-brand-DEFAULT/30 transition-colors'
            : 'flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none focus:text-white focus:bg-slate-800 transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>