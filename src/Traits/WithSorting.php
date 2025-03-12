<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Traits;

use Livewire\Attributes\Url;

trait WithSorting
{
    #[Url]
    public string $sortDir = '';

    #[Url]
    public string $sortField = '';
}
