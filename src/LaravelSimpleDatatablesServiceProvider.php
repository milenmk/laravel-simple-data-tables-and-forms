<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables;

use BladeUI\Icons\Exceptions\CannotRegisterIconSet;
use BladeUI\Icons\Factory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Milenmk\LaravelSimpleDatatables\Commands\MakeTableCommand;

class LaravelSimpleDatatablesServiceProvider extends ServiceProvider
{
    /**
     * @throws BindingResolutionException
     * @throws CannotRegisterIconSet
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-simple-datatables');

        $this->publishes(
            [
                __DIR__ . '/../resources/views/components' => base_path(
                    'resources/views/vendor/laravel-simple-datatables/components',
                ),
            ],
            'laravel-simple-datatables-views',
        );

        Blade::directive('SimpleDatatablesStyle', function () {
            return @vite(['vendor/milenmk/laravel-simple-datatables/resources/css/simple-datatables.css']);
        });

        $this->commands([MakeTableCommand::class]);

        $this->registerBladeIcons();
    }

    /**
     * @throws BindingResolutionException
     * @throws CannotRegisterIconSet
     */
    protected function registerBladeIcons(): void
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
