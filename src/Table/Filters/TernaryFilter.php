<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters;

use Closure;
use Illuminate\View\View;

class TernaryFilter extends BaseFilter
{
    public bool $toggle = true;

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
        return view('laravel-simple-datatables-and-forms::components.table.filters.ternary', ['filter' => $this]);
    }
}
