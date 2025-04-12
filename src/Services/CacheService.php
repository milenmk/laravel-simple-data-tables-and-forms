<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class CacheService
{
    /**
     * Get item from cache or execute callback and store result.
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    public function remember(string $key, callable $callback)
    {
        if (! $this->isCacheEnabled()) {
            return $callback();
        }

        return Cache::remember(
            $this->getCacheKey($key),
            $this->getCacheLifetime(),
            $callback
        );
    }

    /**
     * Store item in cache.
     *
     * @param  mixed  $value
     */
    public function put(string $key, $value): bool
    {
        if (! $this->isCacheEnabled()) {
            return false;
        }

        return Cache::put(
            $this->getCacheKey($key),
            $value,
            $this->getCacheLifetime()
        );
    }

    /**
     * Get item from cache.
     *
     * @param  mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if (! $this->isCacheEnabled()) {
            return $default;
        }

        return Cache::get($this->getCacheKey($key), $default);
    }

    /**
     * Remove item from cache.
     */
    public function forget(string $key): bool
    {
        return Cache::forget($this->getCacheKey($key));
    }

    /**
     * Clear all cache for this package.
     */
    public function clear(): bool
    {
        $prefix = Config::get('simple-datatables.cache.prefix', 'simple_datatables_');
        $keys = Cache::getStore()->many(Cache::getStore()->all());

        $cacheKeys = array_filter(array_keys($keys), function ($key) use ($prefix) {
            return strpos($key, $prefix) === 0;
        });

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        return true;
    }

    /**
     * Get cache key with prefix.
     */
    protected function getCacheKey(string $key): string
    {
        $prefix = Config::get('simple-datatables.cache.prefix', 'simple_datatables_');

        return $prefix . $key;
    }

    /**
     * Get cache lifetime in seconds.
     */
    protected function getCacheLifetime(): int
    {
        return Config::get('simple-datatables.cache.lifetime', 3600);
    }

    /**
     * Check if caching is enabled.
     */
    protected function isCacheEnabled(): bool
    {
        return Config::get('simple-datatables.cache.enable', true);
    }
}
