<?php

namespace LaravelSimpleDatatables\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelSimpleDatatablesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-simple-datatables');
    }

    public function register() {}
}
