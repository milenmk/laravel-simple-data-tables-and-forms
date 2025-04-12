# Caching Strategies

Laravel Simple Datatables implements caching strategies to improve performance, especially for tables with complex configurations or large datasets.

## Cache Configuration

You can configure caching behavior in the `simple-datatables.php` configuration file:

```php
'cache' => [
    // Enable caching for column definitions
    'enable' => true,
    
    // Cache lifetime in seconds (default: 1 hour)
    'lifetime' => 3600,
    
    // Cache key prefix
    'prefix' => 'simple_datatables_',
],
```

## What Gets Cached

The package caches several types of data:

1. **Column Definitions**: The structure and configuration of table columns
2. **Filter Options**: Options for select filters that don't change frequently
3. **Query Results**: For specific combinations of filters, sorting, and pagination

## Using the Cache Service

You can use the cache service directly in your components:

```php
use Milenmk\LaravelSimpleDatatables\Services\CacheService;

public function someMethod()
{
    $cacheService = app(CacheService::class);
    
    // Get cached data or compute it if not cached
    $result = $cacheService->remember('my_cache_key', function() {
        // This will only execute if the data is not in cache
        return $this->expensiveOperation();
    });
    
    // Store data in cache
    $cacheService->put('another_key', $value);
    
    // Get data from cache (with default fallback)
    $data = $cacheService->get('some_key', 'default_value');
    
    // Remove data from cache
    $cacheService->forget('some_key');
    
    // Clear all cache for this package
    $cacheService->clear();
}
```

## Cache Keys

Cache keys are automatically prefixed with the value from the configuration. You should use descriptive keys that include relevant parameters:

```php
$key = "users_table_columns_{$this->componentName}";
$filterKey = "filter_options_department_{$departmentId}";
$queryKey = "users_query_page{$page}_sort{$sortField}_{$sortDir}_search{$search}";
```

## Cache Invalidation

Cache is automatically invalidated when:

1. The cache lifetime expires
2. You manually clear the cache

You should clear relevant cache entries when:

1. Table structure changes
2. Filter options change
3. Underlying data changes significantly

```php
// In your model observer or event listener
public function saved(User $user)
{
    app(CacheService::class)->forget('users_table_data');
}
```

## Performance Tips

1. **Adjust Cache Lifetime**: Set appropriate cache lifetime based on how frequently your data changes.

2. **Cache Selectively**: Cache expensive operations but don't cache everything.

3. **Use Query Caching**: For read-heavy applications, consider using Laravel's query cache in addition to this package's caching.

4. **Monitor Cache Size**: Large caches can consume significant memory. Monitor your cache size and adjust as needed.

5. **Consider Redis**: For production environments, consider using Redis as your cache driver for better performance.