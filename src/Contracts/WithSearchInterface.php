<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Contracts;

interface WithSearchInterface
{
    /**
     * Handle search input update.
     */
    public function updatedSearch(): void;

    /**
     * Change search mode.
     */
    public function setSearchMode(string $mode): void;

    /**
     * Get the current debounce time from config.
     */
    public function searchDebounceTime(): int;

    /**
     * Get the minimum characters required for search.
     */
    public function searchMinCharacters(): int;
}
