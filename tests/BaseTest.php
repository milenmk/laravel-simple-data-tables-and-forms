<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests;

use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\LivewireServiceProvider;
use Milenmk\LaravelSimpleDatatablesAndForms\LaravelSimpleDatatablesAndFormsServiceProvider;
use Mockery;
use Orchestra\Testbench\TestCase;

abstract class BaseTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [LaravelSimpleDatatablesAndFormsServiceProvider::class, LivewireServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Configure the package
        $app['config']->set('simple-datatables-and-forms.export.enable', true);
        $app['config']->set('simple-datatables-and-forms.export.formats', ['csv', 'excel', 'pdf']);
        $app['config']->set('simple-datatables-and-forms.appearance.theme', 'light');
        $app['config']->set('simple-datatables-and-forms.appearance.striped', true);
        $app['config']->set('simple-datatables-and-forms.appearance.hover', true);
        $app['config']->set('simple-datatables-and-forms.appearance.borders', 'all');
        $app['config']->set('simple-datatables-and-forms.appearance.size', 'md');

        // Configure cache settings
        $app['config']->set('simple-datatables-and-forms.cache.enable', true);
        $app['config']->set('simple-datatables-and-forms.cache.lifetime', 3600);
        $app['config']->set('simple-datatables-and-forms.cache.prefix', 'simple_datatables_');
    }
}
