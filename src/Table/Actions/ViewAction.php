<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions;

use Closure;

class ViewAction extends BaseAction
{
    public string|Closure|bool|null $icon = 'heroicon-o-eye';

    /**
     * Set the label for the column. This can be a string, array, or callable.
     */
    public function label(string|array|null $value): self
    {
        $this->label = $value ?? __('View');

        return $this;
    }
}
