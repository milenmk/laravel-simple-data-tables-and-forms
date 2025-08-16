<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Contracts;

use Illuminate\Database\Eloquent\Model;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Form;

interface HasFormInterface
{
    public function form(Form $form): Form;

    public function loadFormModel(?Model $model): void;

    public function fillForm(array $data): void;

    public function getFormData(): array;

    public function setFormData(array $data): void;

    public function resetForm(): void;
}
