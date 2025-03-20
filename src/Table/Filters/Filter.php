<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Table\Filters;

use Closure;
use Illuminate\Support\Facades\View as FacadesView;
use Illuminate\View\View;

class Filter extends BaseFilter
{
    public bool $toggle = false;

    public function toggle(bool $toggle = true): static
    {
        $this->toggle = $toggle;

        return $this;
    }

    public function apply($query, $value): void
    {
        if ($this->query instanceof Closure) {
            call_user_func($this->query, $query, $value);
        } elseif ($value !== null) {
            if ($value === true || $value === 'true') {
                $query->where($this->name, true);
            } elseif ($value === false || $value === 'false') {
                $query->where($this->name, false);
            }
        }
    }

    public function render(): View
    {
        return FacadesView::make('laravel-simple-datatables::components.table.filters.ternary', ['filter' => $this]);
    }
}
