<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables;

use Illuminate\Support\ServiceProvider;

class LaravelSimpleDatatablesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-simple-datatables');

        $this->publishes(
            [
                __DIR__ . '/../resources/views/components' => base_path(
                    'resources/views/vendor/laravel-simple-datatables/components',
                ),
                __DIR__ . '/../resources/views/livewire' => base_path(
                    'resources/views/vendor/laravel-simple-datatables/livewire',
                ),
            ],
            'laravel-simple-datatables-views',
        );
    }

    public function register() {}
}
