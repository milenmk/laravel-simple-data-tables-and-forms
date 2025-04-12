<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Traits;

use Illuminate\Support\Facades\Config;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

/**
 * @see \Milenmk\LaravelSimpleDatatables\Contracts\WithSearchInterface
 */
trait WithSearch
{
    #[Url]
    public string $search = '';

    /**
     * Search mode: 'like', 'exact', or 'fulltext'
     */
    public string $searchMode = '';

    /**
     * Initialize search settings.
     */
    public function initializeWithSearch(): void
    {
        // Set default search mode from config if not already set
        if (empty($this->searchMode)) {
            $this->searchMode = Config::get('simple-datatables.search.default_mode', 'like');
        }
    }

    /**
     * Handle search input update with debounce.
     *
     * Note: The actual debounce is handled in the Blade template with wire:input.debounce
     */
    public function updatedSearch(): void
    {
        // Get minimum characters required from config
        $minChars = Config::get('simple-datatables.search.min_characters', 2);

        // Only reset page if search meets minimum character requirement or is empty
        if (strlen($this->search) >= $minChars || empty($this->search)) {
            $this->resetPage();
        }
    }

    /**
     * Change search mode.
     */
    public function setSearchMode(string $mode): void
    {
        $allowedModes = ['like', 'exact', 'fulltext'];

        if (in_array($mode, $allowedModes)) {
            $this->searchMode = $mode;
            $this->resetPage();
        }
    }

    /**
     * Get the current debounce time from config.
     */
    #[Computed]
    public function searchDebounceTime(): int
    {
        return Config::get('simple-datatables.search.debounce_time', 300);
    }

    /**
     * Get the minimum characters required for search.
     */
    #[Computed]
    public function searchMinCharacters(): int
    {
        return Config::get('simple-datatables.search.min_characters', 2);
    }
}
