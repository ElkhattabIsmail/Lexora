@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-DEFAULT focus:ring focus:ring-brand-DEFAULT/20 transition-colors']) }}>
