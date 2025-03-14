<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Livewire\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class ToggleSwitch extends Component
{
    public Model $model;
    public string $field;
    public bool $value;
    public string $textColor;
    public string $align;

    public function mount(Model $model, string $field): void
    {
        $this->model = $model;
        $this->field = $field;
        $this->value = (bool) $model->{$field};
    }

    public function updatedValue(bool $newValue): void
    {
        // Update the model's field in the database
        $this->model->update([$this->field => $newValue]);

        // Optionally emit an event or provide feedback
        $this->dispatch('toggleUpdated', $this->model->id);
    }

    public function render(): View
    {
        return View::make('livewire.components.toggle');
    }
}
