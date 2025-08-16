<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Contracts;

interface WithSortingInterface
{
    /**
     * Set the sort field and direction.
     */
    public function setSortBy(string $column): void;
}
