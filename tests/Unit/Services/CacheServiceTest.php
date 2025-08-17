<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Services;

use Illuminate\Support\Facades\Cache;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\CacheService;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;
use Psr\SimpleCache\InvalidArgumentException;

class CacheServiceTest extends BaseTest
{
    protected CacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheService = new CacheService;
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Test]
    public function it_can_remember_values()
    {
        $key = 'test_key';
        $value = 'test_value';
        $prefixedKey = 'simple_datatables_' . $key;

        // Clear any existing cache
        Cache::forget($prefixedKey);

        // The first call should execute the callback
        $result = $this->cacheService->remember($key, function () use ($value) {
            return $value;
        });

        $this->assertEquals($value, $result);
        $this->assertTrue(Cache::has($prefixedKey));

        // Change the value to verify the callback isn't executed again
        $value = 'new_value';

        // The second call should return the cached value
        $result = $this->cacheService->remember($key, function () use ($value) {
            return $value;
        });

        // Should still be the old value
        $this->assertEquals('test_value', $result);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Test]
    public function it_can_forget_values()
    {
        $key = 'test_key';
        $value = 'test_value';
        $prefixedKey = 'simple_datatables_' . $key;

        // Set a value in the cache
        Cache::put($prefixedKey, $value);

        // Verify it's there
        $this->assertTrue(Cache::has($prefixedKey));

        // Forget it
        $this->cacheService->forget($key);

        // Verify it's gone
        $this->assertFalse(Cache::has($prefixedKey));
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Test]
    public function it_can_clear_all_cache()
    {
        $key1 = 'test_key1';
        $key2 = 'test_key2';
        $prefixedKey1 = 'simple_datatables_' . $key1;
        $prefixedKey2 = 'simple_datatables_' . $key2;

        // Set some values in the cache
        Cache::put($prefixedKey1, 'value1');
        Cache::put($prefixedKey2, 'value2');

        // Verify they're there
        $this->assertTrue(Cache::has($prefixedKey1));
        $this->assertTrue(Cache::has($prefixedKey2));

        // Clear all cache
        $this->cacheService->clear();

        // Verify they're gone
        $this->assertFalse(Cache::has($prefixedKey1));
        $this->assertFalse(Cache::has($prefixedKey2));
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Test]
    public function it_respects_cache_ttl_from_config()
    {
        // Set a custom TTL in the config
        config(['simple-datatables-and-forms.cache.lifetime' => 5]); // 5 seconds

        $key = 'test_key';
        $value = 'test_value';
        $prefixedKey = 'simple_datatables_' . $key;

        // Remember a value
        $this->cacheService->remember($key, function () use ($value) {
            return $value;
        });

        // Verify it's there
        $this->assertTrue(Cache::has($prefixedKey));

        // Fast-forward time by 6 seconds
        $this->travel(6)->seconds();

        // Verify it's gone
        $this->assertFalse(Cache::has($prefixedKey));
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Test]
    public function it_uses_default_ttl_if_not_configured()
    {
        // Remove the TTL from the config
        config(['simple-datatables-and-forms.cache.lifetime' => null]);

        $key = 'test_key';
        $value = 'test_value';
        $prefixedKey = 'simple_datatables_' . $key;

        // Remember a value
        $this->cacheService->remember($key, function () use ($value) {
            return $value;
        });

        // Verify it's there
        $this->assertTrue(Cache::has($prefixedKey));
    }
}
