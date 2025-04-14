# Advanced Search

Laravel Simple Datatables provides powerful search capabilities that can be customized to fit your needs.

## Search Configuration

You can configure search behavior in the `simple-datatables.php` configuration file:

```php
'search' => [
    // Default search mode: 'like', 'exact', or 'fulltext'
    'default_mode' => 'like',
    
    // Enable full-text search when available
    'enable_fulltext' => false,
    
    // Minimum characters required to trigger search
    'min_characters' => 2,
    
    // Debounce time in milliseconds
    'debounce_time' => 300,
],
```

## Search Modes

### LIKE Search

The default search mode uses SQL `LIKE` queries with wildcards. This is compatible with all database systems but may be slower on large datasets.

```php
// Example of LIKE search in SQL
SELECT * FROM users WHERE name LIKE '%John%'
```

### Exact Match

Exact match search looks for exact matches of the search term. This is faster but less flexible.

```php
// Example of exact match search in SQL
SELECT * FROM users WHERE name = 'John'
```

### Full-Text Search

When enabled and supported by your database, full-text search provides the best performance for large datasets. It requires proper database configuration.

For MySQL:
```sql
-- Create a FULLTEXT index
ALTER TABLE users ADD FULLTEXT(name, email, description);

-- Then the search will use
SELECT * FROM users WHERE MATCH(name, email, description) AGAINST('John' IN BOOLEAN MODE)
```

**Note about PostgreSQL Implementation**: While the documentation mentions PostgreSQL full-text search, the current implementation in `SearchService.php` does not actually use PostgreSQL's full-text search capabilities. Instead, it falls back to using `ILIKE` for PostgreSQL:

```php
// Current implementation for PostgreSQL in SearchService.php
case 'pgsql':
    return $query->where(function ($q) use ($searchableColumns, $search) {
        foreach ($searchableColumns as $column) {
            $q->orWhereRaw("{$column}::text ILIKE ?", ["%{$search}%"]);
        }
    });
```

To implement true PostgreSQL full-text search, you would need to modify the `SearchService` class to use:

```sql
-- PostgreSQL true full-text search syntax
SELECT * FROM users WHERE to_tsvector('english', name || ' ' || email || ' ' || description) @@ to_tsquery('english', 'John')
```

## Implementing Search in Your Components

Search functionality is automatically included in components that use the `HasTable` trait. The search input will appear in the table header.

### Customizing Search Behavior

You can customize search behavior for a specific table by overriding methods in your Livewire component:

```php
// Set minimum characters required for search
public function searchMinCharacters(): int
{
    return 3; // Override the default value
}

// Set debounce time for search input
public function searchDebounceTime(): int
{
    return 500; // Override the default value
}

// Change search mode
public function mount()
{
    $this->searchMode = 'fulltext'; // Use full-text search
}

// Customize search placeholder
public function searchPlaceholder(): string
{
    return 'Search users...';
}
```

### Customizing Searchable Columns

You can specify which columns are searchable when defining your table schema:

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([
            TextColumn::make('name')
                ->searchable(), // This column will be included in search
                
            TextColumn::make('email')
                ->searchable(), // This column will be included in search
                
            TextColumn::make('created_at')
                ->searchable(false), // This column will NOT be included in search
        ]);
}
```

### Advanced Search Options

You can customize how each column is searched:

```php
TextColumn::make('name')
    ->searchable(true, function($query, $searchTerm) {
        // Custom search logic
        return $query->where(function($q) use ($searchTerm) {
            $q->where('first_name', 'like', "%{$searchTerm}%")
              ->orWhere('last_name', 'like', "%{$searchTerm}%");
        });
    })
```

## Search Performance Tips

1. **Use Indexes**: Make sure your searchable columns are properly indexed in the database.

2. **Consider Full-Text Search**: For large datasets, enable full-text search and create appropriate full-text indexes.

3. **Limit Searchable Columns**: Only make necessary columns searchable to improve performance.

4. **Increase Minimum Characters**: Setting a higher minimum character count (3-4) can significantly reduce unnecessary searches.

5. **Adjust Debounce Time**: Increase the debounce time for slower databases or complex queries.