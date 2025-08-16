## Pagination

Laravel Simple Datatables And Forms provides flexible pagination options to help manage large datasets efficiently.

## Basic Configuration

Pagination is enabled by default. You can configure it in the `simple-datatables-and-forms.php` configuration file:

```php
'pagination' => [
    // Default items per page
    'per_page' => 10,

    // Available pagination options
    'options' => [10, 25, 50, 100],

    // Show pagination summary
    'show_summary' => true,

    // Enable pagination by default
    'enabled' => true,
],
```

## Enabling/Disabling Pagination

You can enable or disable pagination for a specific table:

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([
            // Your columns here
        ])
        ->paginate(true); // Enable pagination (default)
        // or
        ->paginate(false); // Disable pagination
}
```

## Customizing Items Per Page

You can set the number of items to display per page:

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([
            // Your columns here
        ])
        ->perPage(25); // Set items per page
}
```

## Customizing Per Page Options

You can customize the available per page options by implementing the `perPageOptions` method in your Livewire component:

```php
public function perPageOptions(): array
{
    return [5, 15, 30, 50, 100]; // Custom options
}
```

## Pagination Summary

By default, the package shows a pagination summary that displays the current range of records being shown and the total
number of records. You can customize this behavior in the configuration file:

```php
'pagination' => [
    // ...
    'show_summary' => true, // Show pagination summary
],
```

## Pagination Controls

The package provides standard pagination controls that allow users to navigate between pages. These controls include:

- First page button
- Previous page button
- Page number buttons
- Next page button
- Last page button

## Customizing Pagination Appearance

You can customize the appearance of pagination controls by publishing the package views:

```bash
php artisan vendor:publish --tag=laravel-simple-datatables-and-forms-views
```

Then edit the pagination component in
`resources/views/vendor/laravel-simple-datatables-and-forms/components/pagination.blade.php`.

## Handling Pagination State

The pagination state is automatically managed by the package. When a user changes the page or items per page, the table
is refreshed with the new data.

## Pagination with Filters and Sorting

Pagination works seamlessly with filters and sorting. When a user applies a filter or sorts a column, the pagination is
reset to the first page to avoid confusion.

## Pagination with multiple table in one view

If you have multiple tables in one view file (presume as nested components i.e. each table comes from a separate
Livewire component), changing the par page value from the dropdown will apply it for the current table. However, since
it is saved as parameter in the URL, upon refreshing the page, all other tables will be affected too. To prevent this,
use unique parameters inside each component like this:

```php
    #[Url(as: 'unique_value_per_page')]
    public int $perPage = 10;

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function table(....
```

## Performance Considerations

For large datasets, consider implementing the following optimizations:

1. **Use Database Pagination**: The package uses Laravel's built-in pagination, which is efficient for large datasets.

2. **Index Your Columns**: Make sure columns used for sorting are properly indexed in your database.

3. **Limit Eager Loading**: Be careful with eager loading relationships, as it can impact pagination performance.

4. **Consider Caching**: For very large datasets, consider implementing caching as described in
   the [Caching](caching.md) documentation.

## Example: Complete Pagination Implementation

Here's a complete example of implementing pagination in a table:

```php
// In your Livewire component
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([
            // Your columns here
        ])
        ->paginate(true) // Enable pagination
        ->perPage(15); // Set items per page
}

// Custom per page options
public function perPageOptions(): array
{
    return [10, 15, 25, 50, 100];
}
```

This implementation provides a paginated table with custom per page options, allowing users to efficiently navigate
through large datasets.
