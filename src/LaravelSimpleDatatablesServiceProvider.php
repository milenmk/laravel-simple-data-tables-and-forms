<?php

namespace Milenmk\LaravelSimpleDatatables;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Milenmk\LaravelSimpleDatatables\Livewire\Components\Checkbox;
use Milenmk\LaravelSimpleDatatables\Livewire\Components\ToggleSwitch;

class LaravelSimpleDatatablesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-simple-datatables');
        Livewire::component('laravel-simple-datatables::toggle-switch', ToggleSwitch::class);
        Livewire::component('laravel-simple-datatables::checkbox', Checkbox::class);

        $this->publishes([
            __DIR__ . '/../resources/views/components' => base_path('resources/views/vendor/laravel-simple-datatables/components'),
            __DIR__ . '/../resources/views/livewire' => base_path('resources/views/vendor/laravel-simple-datatables/livewire'),
        ], 'laravel-simple-datatables-views');
    }

    public function register() {}
}
