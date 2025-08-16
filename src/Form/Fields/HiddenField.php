<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields;

use Illuminate\Support\ViewErrorBag;
use Throwable;

class HiddenField extends Field
{
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->type = 'hidden';
    }

    /**
     * @throws Throwable
     */
    public function render(): string
    {
        return view('laravel-simple-datatables-and-forms::components.form.fields.hidden', [
            'field' => $this,
            'errors' => session()->get('errors', new ViewErrorBag),
        ])->render();
    }
}
