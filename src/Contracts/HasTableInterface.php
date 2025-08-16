<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;

interface HasTableInterface
{
    /**
     * Define the table structure.
     */
    public function table(Table $table): Table;

    /**
     * Get the table property.
     */
    public function getTableProperty(): View;

    /**
     * Get a model by ID.
     */
    public function getModel(string $itemId): ?Model;

    /**
     * Toggle a boolean value for a model.
     */
    public function toggleValue(string $itemId, string $field): void;

    /**
     * Toggle column visibility.
     */
    public function toggleColumnVisibility(string $columnKey): void;
}
