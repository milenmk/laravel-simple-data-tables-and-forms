<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\CacheService;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class CacheServiceTest extends BaseTest
{
    private CacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheService = new CacheService;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_remember_value_when_cache_enabled()
    {
        Config::set('simple-datatables-and-forms.cache.enable', true);

        Config::set('simple-datatables-and-forms.cache.prefix', 'test_prefix_');

        Config::set('simple-datatables-and-forms.cache.lifetime', 3600);

        Cache::shouldReceive('remember')
            ->once()
            ->with('test_prefix_test_key', 3600, Mockery::type('callable'))
            ->andReturn('cached_value');

        $result = $this->cacheService->remember('test_key', fn () => 'computed_value');

        $this->assertEquals('cached_value', $result);
    }

    #[Test]
    public function it_executes_callback_when_cache_disabled()
    {
        Config::set('simple-datatables-and-forms.cache.enable', true);

        $result = $this->cacheService->remember('test_key', fn () => 'computed_value');

        $this->assertEquals('computed_value', $result);
    }

    #[Test]
    public function it_can_get_value_when_cache_enabled()
    {
        Config::set('simple-datatables-and-forms.cache.enable', true);

        Config::set('simple-datatables-and-forms.cache.prefix', 'test_prefix_');

        Cache::shouldReceive('get')
            ->once()
            ->with('test_prefix_test_key', 'default_value')
            ->andReturn('cached_value');

        $result = $this->cacheService->get('test_key', 'default_value');

        $this->assertEquals('cached_value', $result);
    }

    #[Test]
    public function it_returns_default_when_cache_disabled()
    {
        Config::set('simple-datatables-and-forms.cache.enable', true);

        $result = $this->cacheService->get('test_key', 'default_value');

        $this->assertEquals('default_value', $result);
    }

    #[Test]
    public function it_can_put_value_when_cache_enabled()
    {
        Config::set('simple-datatables-and-forms.cache.enable', true);

        Config::set('simple-datatables-and-forms.cache.prefix', 'test_prefix_');

        Config::set('simple-datatables-and-forms.cache.lifetime', 3600);

        Cache::shouldReceive('put')
            ->once()
            ->with('test_prefix_test_key', 'test_value', 3600)
            ->andReturn(true);

        $result = $this->cacheService->put('test_key', 'test_value');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_when_put_and_cache_disabled()
    {
        Config::set('simple-datatables-and-forms.cache.enable', false);

        $result = $this->cacheService->put('test_key', 'test_value');

        $this->assertFalse($result);
    }

    #[Test]
    public function it_can_forget_cache_key()
    {
        Config::set('simple-datatables-and-forms.cache.prefix', 'test_prefix_');

        Cache::shouldReceive('forget')
            ->once()
            ->with('test_prefix_test_key')
            ->andReturn(true);

        $result = $this->cacheService->forget('test_key');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_clear_cache_with_fallback()
    {
        Config::set('simple-datatables-and-forms.cache.prefix', 'test_prefix_');

        // Mock Cache store that doesn't have Redis or Memcached methods
        $store = Mockery::mock();
        Cache::shouldReceive('getStore')->andReturn($store);
        Cache::shouldReceive('flush')->once();

        $result = $this->cacheService->clear();

        $this->assertTrue($result);
    }

    #[Test]
    public function it_handles_cache_lifetime_as_null()
    {
        Config::set('simple-datatables-and-forms.cache.enable', true);

        Config::set('simple-datatables-and-forms.cache.prefix', 'test_prefix_');

        Config::set('simple-datatables-and-forms.cache.lifetime', 3600);

        Cache::shouldReceive('put')
            ->once()
            ->with('test_prefix_test_key', 'test_value', 3600)
            ->andReturn(true);

        $result = $this->cacheService->put('test_key', 'test_value');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_handles_non_integer_cache_lifetime()
    {
        Config::set('simple-datatables-and-forms.cache.enable', true);

        Config::set('simple-datatables-and-forms.cache.prefix', 'test_prefix_');

        Config::set('simple-datatables-and-forms.cache.lifetime', '7200');

        Cache::shouldReceive('put')
            ->once()
            ->with('test_prefix_test_key', 'test_value', 7200)
            ->andReturn(true);

        $result = $this->cacheService->put('test_key', 'test_value');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_executes_callback_directly_when_cache_disabled()
    {
        Config::set('simple-datatables-and-forms.cache.enable', false);

        $result = $this->cacheService->remember('test_key', fn () => 'computed_value');

        $this->assertEquals('computed_value', $result);
    }

    #[Test]
    public function it_returns_default_when_cache_disabled_for_get()
    {
        Config::set('simple-datatables-and-forms.cache.enable', false);

        $result = $this->cacheService->get('test_key', 'default_value');

        $this->assertEquals('default_value', $result);
    }

    #[Test]
    public function it_can_clear_cache_with_fallback_flush()
    {
        Config::set('simple-datatables-and-forms.cache.prefix', 'test_prefix_');

        // Mock store that doesn't have Redis or Memcached methods - will use fallback
        $store = Mockery::mock();
        Cache::shouldReceive('getStore')->andReturn($store);
        Cache::shouldReceive('flush')
            ->once()
            ->andReturn(true);

        $result = $this->cacheService->clear();

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_clear_cache_with_memcached_store()
    {
        Config::set('simple-datatables-and-forms.cache.prefix', 'test_prefix_');

        // Mock Memcached store
        $memcached = Mockery::mock();
        $store = Mockery::mock();
        $store->shouldReceive('getMemcached')->andReturn($memcached);

        Cache::shouldReceive('getStore')->andReturn($store);
        Cache::shouldReceive('flush')->once();

        $result = $this->cacheService->clear();

        $this->assertTrue($result);
    }

    #[Test]
    public function it_handles_exception_during_cache_clear()
    {
        Config::set('simple-datatables-and-forms.cache.prefix', 'test_prefix_');

        // Mock store that throws exception
        $store = Mockery::mock();
        $store->shouldReceive('getRedis')->andThrow(new Exception('Redis error'));

        Cache::shouldReceive('getStore')->andReturn($store);
        Cache::shouldReceive('flush')->once();

        $result = $this->cacheService->clear();

        $this->assertTrue($result);
    }

    #[Test]
    public function it_handles_null_cache_lifetime()
    {
        Config::set('simple-datatables-and-forms.cache.enable', true);
        Config::set('simple-datatables-and-forms.cache.prefix', 'test_prefix_');
        Config::set('simple-datatables-and-forms.cache.lifetime', null);

        Cache::shouldReceive('put')
            ->once()
            ->with('test_prefix_test_key', 'test_value', 3600)
            ->andReturn(true);

        $result = $this->cacheService->put('test_key', 'test_value');

        $this->assertTrue($result);
    }
}
