<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-brand-DEFAULT border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-dark focus:bg-brand-dark active:bg-brand-dark focus:outline-none focus:ring-2 focus:ring-brand-DEFAULT focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm']) }}>
    {{ $slot }}
</button>
