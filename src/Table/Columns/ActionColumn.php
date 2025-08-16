<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns;

class ActionColumn extends Column
{
    public bool $groupActions = false;

    protected array $actions = [];
    protected ?string $actionView = null;

    protected string $view = 'laravel-simple-datatables-and-forms::components.table.columns.action';

    public function actions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function groupActions(): static
    {
        $this->groupActions = true;

        return $this;
    }
}
