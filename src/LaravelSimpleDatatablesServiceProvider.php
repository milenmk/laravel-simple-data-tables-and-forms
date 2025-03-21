<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables;

use BladeUI\Icons\Factory;
use Illuminate\Support\ServiceProvider;
use Milenmk\LaravelSimpleDatatables\Commands\MakeTableCommand;

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

        $this->commands([MakeTableCommand::class]);

        $this->registerBladeIcons();
    }

    public function register() {}

    protected function registerBladeIcons()
    {
        // Check if the Blade Icons package is installed
        if (! class_exists(Factory::class)) {
            return;
        }

        /** @var Factory $factory */
        $factory = $this->app->make(Factory::class);

        // Resolve path to Heroicons inside your package
        $heroiconsPath = __DIR__ . '/../../vendor/blade-ui-kit/blade-heroicons/resources/svg';

        if (! is_dir($heroiconsPath)) {
            return; // Prevent errors if the package is missing
        }

        // Register Heroicons inside Blade Icons
        $factory->add('heroicons', [
            'path' => $heroiconsPath,
            'prefix' => 'heroicon', // Allows usage like 'heroicon-o-check-circle'
        ]);
    }
}
