<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms;

use BladeUI\Icons\Factory;
use BladeUI\Icons\IconsManifest;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Milenmk\LaravelSimpleDatatablesAndForms\Commands\MakeFormCommand;
use Milenmk\LaravelSimpleDatatablesAndForms\Commands\MakeTableCommand;
use Milenmk\LaravelSimpleDatatablesAndForms\Commands\PublishAssetsCommand;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\AssetService;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\CacheService;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\ExportService;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\SearchService;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\SecurityService;

class LaravelSimpleDatatablesAndFormsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/../config/simple-datatables-and-forms.php', 'simple-datatables-and-forms');

        // Register the package services
        $this->app->singleton(AssetService::class, function () {
            return new AssetService;
        });

        $this->app->singleton(CacheService::class, function () {
            return new CacheService;
        });

        $this->app->singleton(ExportService::class, function () {
            return new ExportService;
        });

        $this->app->singleton(SearchService::class, function () {
            return new SearchService;
        });

        $this->app->singleton(SecurityService::class, function () {
            return new SecurityService;
        });
    }

    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-simple-datatables-and-forms');

        // Publish views
        $this->publishes(
            [
                __DIR__ . '/../resources/views/components' => base_path(
                    'resources/views/vendor/laravel-simple-datatables-and-forms/components',
                ),
                __DIR__ . '/../resources/views/exports' => base_path(
                    'resources/views/vendor/laravel-simple-datatables-and-forms/exports',
                ),
            ],
            'laravel-simple-datatables-and-forms-views',
        );

        // Publish CSS and JS
        $this->publishes(
            [
                __DIR__ . '/../public/css' => public_path('vendor/milenmk/laravel-simple-datatables-and-forms/css'),
                __DIR__ . '/../public/js' => public_path('vendor/milenmk/laravel-simple-datatables-and-forms/js'),
            ],
            'laravel-simple-datatables-and-forms-assets',
        );

        // Publish config
        $this->publishes(
            [
                __DIR__ . '/../config/simple-datatables-and-forms.php' => config_path(
                    'simple-datatables-and-forms.php',
                ),
            ],
            'laravel-simple-datatables-and-forms-config',
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
        $this->commands([MakeTableCommand::class, MakeFormCommand::class, PublishAssetsCommand::class]);

        // Register Blade icons
        $this->registerBladeIcons();
    }

    /**
     * @throws BindingResolutionException
     */
    protected function registerBladeIcons(): void
    {
        // Check if the Blade Icons package is installed
        if (! class_exists(Factory::class)) {
            return;
        }

        // Register the IconsManifest with the container
        if (! $this->app->bound('blade.icons')) {
            $this->app->singleton('blade.icons', function ($app) {
                $config = $app['config']['blade-icons'] ?? [];
                $manifestPath = $config['manifest'] ?? storage_path('blade-icons.php');

                return new IconsManifest($manifestPath);
            });
        }

        try {
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
        } catch (Exception $e) {
            // Log the error but don't crash the application
            if ($this->app->bound('log')) {
                $this->app->make('log')->error('Failed to register Blade Icons: ' . $e->getMessage());
            }
        }
    }
}
