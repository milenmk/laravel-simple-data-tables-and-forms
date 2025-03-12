<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Traits;

use Livewire\Attributes\Url;

trait WithSearch
{
    #[Url]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
