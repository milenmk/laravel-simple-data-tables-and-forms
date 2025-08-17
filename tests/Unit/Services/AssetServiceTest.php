<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Services;

use Illuminate\Support\Facades\Config;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\AssetService;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;

class AssetServiceTest extends BaseTest
{
    private AssetService $assetService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->assetService = new AssetService;
    }

    /**
     * @test
     */
    public function get_css_path_returns_correct_path(): void
    {
        $path = $this->assetService->getCssPath();

        $this->assertStringContainsString('vendor/milenmk/laravel-simple-datatables-and-forms/css/package.css', $path);
    }

    /**
     * @test
     */
    public function get_js_path_returns_correct_path(): void
    {
        $path = $this->assetService->getJsPath();

        $this->assertStringContainsString('vendor/milenmk/laravel-simple-datatables-and-forms/js/package.js', $path);
    }

    /**
     * @test
     */
    public function get_css_tag_with_lazy_load_enabled(): void
    {
        Config::set('simple-datatables-and-forms.assets.lazy_load', true);

        $tag = $this->assetService->getCssTag();

        $this->assertStringContainsString('rel="preload"', $tag);
        $this->assertStringContainsString('as="style"', $tag);
        $this->assertStringContainsString('onload="this.onload=null;this.rel=\'stylesheet\'"', $tag);
        $this->assertStringContainsString('<noscript>', $tag);
        $this->assertStringContainsString('crossorigin', $tag);
    }

    /**
     * @test
     */
    public function get_css_tag_with_lazy_load_disabled(): void
    {
        Config::set('simple-datatables-and-forms.assets.lazy_load', false);

        $tag = $this->assetService->getCssTag();

        $this->assertStringContainsString('rel="stylesheet"', $tag);
        $this->assertStringContainsString('crossorigin', $tag);
        $this->assertStringNotContainsString('preload', $tag);
        $this->assertStringNotContainsString('onload', $tag);
        $this->assertStringNotContainsString('<noscript>', $tag);
    }

    /**
     * @test
     */
    public function get_js_tag_with_defer_enabled(): void
    {
        Config::set('simple-datatables-and-forms.assets.defer_js', true);

        $tag = $this->assetService->getJsTag();

        $this->assertStringContainsString('<script', $tag);
        $this->assertStringContainsString('defer', $tag);
        $this->assertStringContainsString('crossorigin', $tag);
        $this->assertStringContainsString('</script>', $tag);
    }

    /**
     * @test
     */
    public function get_js_tag_with_defer_disabled(): void
    {
        Config::set('simple-datatables-and-forms.assets.defer_js', false);

        $tag = $this->assetService->getJsTag();

        $this->assertStringContainsString('<script', $tag);
        $this->assertStringContainsString('crossorigin', $tag);
        $this->assertStringContainsString('</script>', $tag);
        $this->assertStringNotContainsString('defer', $tag);
    }

    /**
     * @test
     */
    public function css_and_js_tags_contain_correct_paths(): void
    {
        // Config::shouldReceive('get')->andReturn(true);

        $cssTag = $this->assetService->getCssTag();
        $jsTag = $this->assetService->getJsTag();

        $this->assertStringContainsString('package.css', $cssTag);
        $this->assertStringContainsString('package.js', $jsTag);
    }
}
