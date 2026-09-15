/* 

Moved searchableSelect into 

app.js
 using Alpine.data('searchableSelect', ...) — this registers it as a named component before Alpine.start(),
  so it's always available when Alpine processes the page.
Removed the now-redundant @push('scripts') blocks from both view files.
The dropdown now works correctly: clicking focuses the field and shows all options,
 and typing filters the list to matching names.

 Using in dossiers.create and 
  dossiers.edit
  
 */

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
