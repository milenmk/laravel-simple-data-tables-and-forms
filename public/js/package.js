/**
 * Laravel Simple DataTables - JavaScript Package
 * 
 * This file contains all JavaScript functionality for the package,
 * including Alpine.js components and utilities.
 */

// Searchable Select Alpine.js Component
function searchableSelect(config = {}) {
    return {
        // Configuration
        options: config.options || [],
        value: config.value || (config.multiple ? [] : ''),
        multiple: config.multiple || false,
        placeholder: config.placeholder || 'Select an option...',
        searchPlaceholder: config.searchPlaceholder || 'Search options...',
        emptyText: config.emptyText || 'No options found',
        
        // State
        isOpen: false,
        searchQuery: '',
        highlightedIndex: -1,
        
        // Computed
        get filteredOptions() {
            if (!this.searchQuery) {
                return this.options;
            }
            
            return this.options.filter(option => {
                const label = typeof option === 'object' ? option.label : option;
                return label.toLowerCase().includes(this.searchQuery.toLowerCase());
            });
        },
        
        get selectedOptions() {
            if (!this.multiple) {
                const selected = this.options.find(option => {
                    const value = typeof option === 'object' ? option.value : option;
                    return value == this.value;
                });
                return selected ? [selected] : [];
            }
            
            return this.options.filter(option => {
                const value = typeof option === 'object' ? option.value : option;
                return this.value.includes(value);
            });
        },
        
        get displayText() {
            if (this.selectedOptions.length === 0) {
                return this.placeholder;
            }
            
            if (!this.multiple) {
                const option = this.selectedOptions[0];
                return typeof option === 'object' ? option.label : option;
            }
            
            if (this.selectedOptions.length === 1) {
                const option = this.selectedOptions[0];
                return typeof option === 'object' ? option.label : option;
            }
            
            return `${this.selectedOptions.length} selected`;
        },
        
        // Methods
        init() {
            // Watch for external value changes (e.g., from Livewire)
            this.$watch('value', () => {
                this.$dispatch('change', this.value);
            });
            
            // Close dropdown when clicking outside
            this.$el.addEventListener('click', (e) => e.stopPropagation());
            document.addEventListener('click', () => {
                this.isOpen = false;
            });
            
            // Keyboard navigation
            this.$el.addEventListener('keydown', (e) => {
                this.handleKeydown(e);
            });
        },
        
        toggle() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.searchQuery = '';
                this.highlightedIndex = -1;
                this.$nextTick(() => {
                    this.$refs.searchInput?.focus();
                });
            }
        },
        
        selectOption(option) {
            const value = typeof option === 'object' ? option.value : option;
            
            if (this.multiple) {
                if (this.value.includes(value)) {
                    this.value = this.value.filter(v => v !== value);
                } else {
                    this.value = [...this.value, value];
                }
            } else {
                this.value = value;
                this.isOpen = false;
            }
        },
        
        removeOption(option) {
            if (!this.multiple) return;
            
            const value = typeof option === 'object' ? option.value : option;
            this.value = this.value.filter(v => v !== value);
        },
        
        clear() {
            this.value = this.multiple ? [] : '';
            this.isOpen = false;
        },
        
        handleKeydown(e) {
            if (!this.isOpen && (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown')) {
                e.preventDefault();
                this.toggle();
                return;
            }
            
            if (!this.isOpen) return;
            
            switch (e.key) {
                case 'Escape':
                    e.preventDefault();
                    this.isOpen = false;
                    break;
                    
                case 'ArrowDown':
                    e.preventDefault();
                    this.highlightedIndex = Math.min(
                        this.highlightedIndex + 1,
                        this.filteredOptions.length - 1
                    );
                    this.scrollToHighlighted();
                    break;
                    
                case 'ArrowUp':
                    e.preventDefault();
                    this.highlightedIndex = Math.max(this.highlightedIndex - 1, 0);
                    this.scrollToHighlighted();
                    break;
                    
                case 'Enter':
                    e.preventDefault();
                    if (this.highlightedIndex >= 0 && this.filteredOptions[this.highlightedIndex]) {
                        this.selectOption(this.filteredOptions[this.highlightedIndex]);
                    }
                    break;
            }
        },
        
        scrollToHighlighted() {
            this.$nextTick(() => {
                const highlighted = this.$refs.dropdown?.querySelector('[data-highlighted="true"]');
                if (highlighted) {
                    highlighted.scrollIntoView({ block: 'nearest' });
                }
            });
        },
        
        isSelected(option) {
            const value = typeof option === 'object' ? option.value : option;
            
            if (this.multiple) {
                return this.value.includes(value);
            }
            
            return this.value == value;
        },
        
        getOptionLabel(option) {
            return typeof option === 'object' ? option.label : option;
        },
        
        getOptionValue(option) {
            return typeof option === 'object' ? option.value : option;
        }
    };
}

// Auto-register with Alpine if available
document.addEventListener('alpine:init', () => {
    if (window.Alpine) {
        window.Alpine.data('searchableSelect', searchableSelect);
    }
});

// Also register immediately if Alpine is already loaded
if (typeof window !== 'undefined' && window.Alpine) {
    window.Alpine.data('searchableSelect', searchableSelect);
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { searchableSelect };
}