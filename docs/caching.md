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

### Automatic Cache Invalidation

You can set up automatic cache invalidation by implementing model observers or using Laravel's model events:

```php
// In your AppServiceProvider or a dedicated observer
public function boot()
{
    User::observe(UserObserver::class);
    
    // Or using closures
    User::created(function ($user) {
        app(CacheService::class)->forget('users_table_data');
    });
    
    User::updated(function ($user) {
        app(CacheService::class)->forget('users_table_data');
    });
    
    User::deleted(function ($user) {
        app(CacheService::class)->forget('users_table_data');
    });
}
```

### Manual Cache Invalidation

You should manually clear relevant cache entries when:

1. Table structure changes
2. Filter options change
3. Underlying data changes significantly

```php
// In your model observer or event listener
public function saved(User $user)
{
    app(CacheService::class)->forget('users_table_data');
}

// In your controller after a bulk operation
public function bulkDelete()
{
    // Perform bulk delete
    User::whereIn('id', $ids)->delete();
    
    // Clear cache
    app(CacheService::class)->forget('users_table_data');
}
```

### Selective Cache Invalidation

For more granular control, you can invalidate specific cache entries based on the affected data:

```php
// In your model observer
public function saved(User $user)
{
    $cacheService = app(CacheService::class);
    
    // Clear specific user cache
    $cacheService->forget("user_data_{$user->id}");
    
    // Clear department-specific cache if department changed
    if ($user->isDirty('department_id')) {
        $cacheService->forget("department_users_{$user->department_id}");
        
        if ($user->getOriginal('department_id')) {
            $cacheService->forget("department_users_{$user->getOriginal('department_id')}");
        }
    }
    
    // Only clear global cache for significant changes
    if ($user->isDirty(['role', 'is_active'])) {
        $cacheService->forget('users_table_data');
    }
}
```

### Cache Tags (Not Currently Implemented)

**Important Note**: While Laravel supports cache tags with certain cache drivers (like Redis or Memcached), the current implementation of `CacheService` in this package does not include tag support. The following is an example of how you might implement tag support if needed:

```php
// This functionality is NOT currently available in the package
// You would need to extend the CacheService class to implement this

// Example of how you might implement tag support:
public function someMethod()
{
    // Using Laravel's cache facade directly for tags
    Cache::tags(['users', "user-{$userId}"])->put('key', $value, 3600);
    
    // Retrieve with tags
    $value = Cache::tags(['users', "user-{$userId}"])->get('key');
    
    // Invalidate by tag
    Cache::tags(['users'])->flush(); // Clear all user-related cache
    Cache::tags(["user-{$userId}"])->flush(); // Clear specific user cache
}
```

If you need tag support, consider extending the `CacheService` class to add this functionality or use Laravel's Cache facade directly in your application code.

## Performance Tips

1. **Adjust Cache Lifetime**: Set appropriate cache lifetime based on how frequently your data changes.

2. **Cache Selectively**: Cache expensive operations but don't cache everything.

3. **Use Query Caching**: For read-heavy applications, consider using Laravel's query cache in addition to this package's caching.

4. **Monitor Cache Size**: Large caches can consume significant memory. Monitor your cache size and adjust as needed.

5. **Consider Redis**: For production environments, consider using Redis as your cache driver for better performance.