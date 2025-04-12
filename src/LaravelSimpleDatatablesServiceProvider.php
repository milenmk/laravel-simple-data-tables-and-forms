<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables;

use BladeUI\Icons\Exceptions\CannotRegisterIconSet;
use BladeUI\Icons\Factory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Milenmk\LaravelSimpleDatatables\Commands\MakeTableCommand;
use Milenmk\LaravelSimpleDatatables\Commands\PublishAssetsCommand;
use Milenmk\LaravelSimpleDatatables\Services\AssetService;

class LaravelSimpleDatatablesServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/../config/simple-datatables.php', 'simple-datatables');

        // Register the asset service
        $this->app->singleton(AssetService::class, function ($app) {
            return new AssetService;
        });
    }

    /**
     * @throws BindingResolutionException
     * @throws CannotRegisterIconSet
     */
    public function boot(): void
    {
        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-simple-datatables');

        // Publish views
        $this->publishes(
            [
                __DIR__ . '/../resources/views/components' => base_path(
                    'resources/views/vendor/laravel-simple-datatables/components',
                ),
            ],
            'laravel-simple-datatables-views',
        );

        // Publish CSS and JS
        $this->publishes(
            [
                __DIR__ . '/../public/css' => public_path('vendor/milenmk/laravel-simple-datatables/css'),
                __DIR__ . '/../public/js' => public_path('vendor/milenmk/laravel-simple-datatables/js'),
            ],
            'laravel-simple-datatables-assets',
        );

        // Publish config
        $this->publishes(
            [
                __DIR__ . '/../config/simple-datatables.php' => config_path('simple-datatables.php'),
            ],
            'laravel-simple-datatables-config',
        );

        // Register Blade directive for CSS
        Blade::directive('SimpleDatatablesStyle', function () {
            $assetService = app(AssetService::class);

            return $assetService->getCssTag();
        });

        // Register Blade directive for JavaScript
        Blade::directive('SimpleDatatablesScript', function () {
            $assetService = app(AssetService::class);

            return $assetService->getJsTag();
        });

        // Register commands
        $this->commands([MakeTableCommand::class, PublishAssetsCommand::class]);

        // Register Blade icons
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
