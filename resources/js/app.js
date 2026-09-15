
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('searchableSelect', ({ options, selected, placeholder }) => ({
    options,
    placeholder,
    query: '',
    open: false,
    selectedValue: selected || '',
    selectedLabel: '',
    init() {
        if (this.selectedValue) {
            const found = this.options.find(o => String(o.value) === String(this.selectedValue));
            if (found) this.selectedLabel = found.label;
        }
    },
    get filtered() {
        const q = this.query.toLowerCase();
        if (!q) return this.options;
        return this.options.filter(o => o.label.toLowerCase().includes(q));
    },
    select(opt) {
        this.selectedValue = opt.value;
        this.selectedLabel = opt.label;
        this.query = '';
        this.open = false;
    },
    handleBlur() {
        setTimeout(() => { this.open = false; this.query = ''; }, 150);
    },
}));

Alpine.start();
