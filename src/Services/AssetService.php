<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Services;

use Illuminate\Support\Facades\Config;

class AssetService
{
    /**
     * Get CSS HTML tag with appropriate loading strategy.
     */
    public function getCssTag(): string
    {
        $cssPath = $this->getCssPath();
        $lazyLoad = Config::get('simple-datatables.assets.lazy_load', true);

        if ($lazyLoad) {
            return "<link rel=\"preload\" as=\"style\" href=\"{$cssPath}\" onload=\"this.onload=null;this.rel='stylesheet'\" crossorigin><noscript><link rel=\"stylesheet\" href=\"{$cssPath}\"></noscript>";
        }

        return "<link rel=\"stylesheet\" href=\"{$cssPath}\" crossorigin>";
    }

    /**
     * Get the CSS path.
     */
    public function getCssPath(): string
    {
        // Return the path to the CSS file in the vendor directory
        return asset('vendor/milenmk/laravel-simple-datatables/css/package.css');
    }

    /**
     * Get JS HTML tag with appropriate loading strategy.
     */
    public function getJsTag(): string
    {
        $jsPath = $this->getJsPath();
        $deferJs = Config::get('simple-datatables.assets.defer_js', true);

        if ($deferJs) {
            return "<script src=\"{$jsPath}\" defer crossorigin></script>";
        }

        return "<script src=\"{$jsPath}\" crossorigin></script>";
    }

    /**
     * Get the JavaScript path.
     */
    public function getJsPath(): string
    {
        // Return the path to the JS file in the vendor directory
        return asset('vendor/milenmk/laravel-simple-datatables/js/package.js');
    }
}
