<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

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

        return Cache::remember($this->getCacheKey($key), $this->getCacheLifetime(), $callback);
    }

    /**
     * Get item from cache.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (! $this->isCacheEnabled()) {
            return $default;
        }

        return Cache::get($this->getCacheKey($key), $default);
    }

    /**
     * Store item in cache.
     */
    public function put(string $key, mixed $value): bool
    {
        if (! $this->isCacheEnabled()) {
            return false;
        }

        return Cache::put($this->getCacheKey($key), $value, $this->getCacheLifetime());
    }

    /**
     * Clear all cache for this package.
     *
     * Note: This method uses a driver-specific approach to clear cache entries with a specific prefix.
     */
    public function clear(): bool
    {
        $prefix = Config::get('simple-datatables.cache.prefix', 'simple_datatables_');

        try {
            // For Redis driver
            $store = Cache::getStore();
            if (method_exists($store, 'getRedis')) {
                $redis = $store->getRedis();
                $keys = $redis->keys("*{$prefix}*");

                foreach ($keys as $key) {
                    $redis->del($key);
                }

                return true;
            }

            // For Memcached driver
            if (method_exists($store, 'getMemcached')) {
                // Memcached doesn't support pattern-based deletion
                // We'll use Cache::flush() as a fallback
                Cache::flush();

                return true;
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        // Fallback: Use Cache::flush() as a last resort
        // Warning: This clears ALL cache entries
        Cache::flush();

        return true;
    }

    /**
     * Remove item from cache.
     */
    public function forget(string $key): bool
    {
        return Cache::forget($this->getCacheKey($key));
    }

    /**
     * Check if caching is enabled.
     */
    protected function isCacheEnabled(): bool
    {
        return Config::get('simple-datatables.cache.enable', true);
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
        $lifetime = Config::get('simple-datatables.cache.lifetime', 3600);

        // Ensure we always return an integer
        return $lifetime !== null ? (int) $lifetime : 3600;
    }
}
