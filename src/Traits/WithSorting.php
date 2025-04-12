<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Traits;

use Livewire\Attributes\Url;

/**
 * @see \Milenmk\LaravelSimpleDatatables\Contracts\WithSortingInterface
 */
trait WithSorting
{
    #[Url]
    public string $sortDir = '';

    #[Url]
    public string $sortField = '';

    /**
     * Set the sort field and direction.
     */
    public function setSortBy(string $column): void
    {
        if ($this->sortField === $column) {
            $this->sortDir = $this->sortDir === 'ASC' ? 'DESC' : 'ASC';

            return;
        }

        $this->sortField = $column;
        $this->sortDir = 'ASC';
    }
}
