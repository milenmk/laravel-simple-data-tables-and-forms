<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Traits;

use Livewire\Attributes\Url;

trait WithPerPage
{
    #[Url]
    public int $perPage = 10;

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }
}
