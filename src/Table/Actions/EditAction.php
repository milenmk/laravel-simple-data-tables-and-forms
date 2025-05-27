<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Table\Actions;

use Closure;

class EditAction extends BaseAction
{
    public string|Closure|bool|null $icon = 'heroicon-o-pencil-square';

    /**
     * Set the label for the column. This can be a string, array, or callable.
     */
    public function label(string|array|null $value): self
    {
        $this->label = $value ?? __('Edit');

        return $this;
    }
}
