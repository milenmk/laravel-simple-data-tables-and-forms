<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Table\Columns;

class ActionColumn extends Column
{
    protected array $actions = [];

    protected bool $groupActions = false;

    protected ?string $actionView = null;

    protected string $view = 'laravel-simple-datatables::components.table.columns.action';

    public function actions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function groupActions(): void
    {
        $this->groupActions = true;
    }
}
