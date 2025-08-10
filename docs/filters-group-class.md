# FiltersGroup Class

The `FiltersGroup` class provides a more structured way to group filters together, offering better organization and control over filter grouping compared to the individual `group()` method approach.

## Overview

The `FiltersGroup` class allows you to:

- Group multiple filters together using a dedicated class
- Control group label visibility with `hideGroupLabel()` method
- Set custom group labels
- Organize filters in a more readable and maintainable way

## Basic Usage

### Creating a FiltersGroup

```php
use Milenmk\LaravelSimpleDatatables\Table\Filters\FiltersGroup;
use Milenmk\LaravelSimpleDatatables\Table\Filters\TernaryFilter;
use Milenmk\LaravelSimpleDatatables\Table\Filters\SelectFilter;

public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->filters([
            // Using FiltersGroup
            FiltersGroup::make('some_name')->schema([
                TernaryFilter::make('filter1'),
                TernaryFilter::make('filter2'),
                TernaryFilter::make('filter3'),
            ]),

            // Individual filter
            SelectFilter::make('filter4')
        ])
        ->filterColumns(2); // 2 columns: 1 for the group, 1 for filter4
}
```

### Hiding Group Labels

You can hide the group label using the `hideGroupLabel()` method:

```php
FiltersGroup::make('status_filters')
    ->schema([TernaryFilter::make('is_active')->label('Active'), TernaryFilter::make('is_verified')->label('Verified')])
    ->hideGroupLabel();
```

### Custom Group Labels

Set a custom label for the group:

```php
FiltersGroup::make('user_status')
    ->label('User Status Filters') // Custom label instead of 'user_status'
    ->schema([
        TernaryFilter::make('is_admin')->label('Administrator'),
        TernaryFilter::make('is_verified')->label('Email Verified'),
    ]);
```

## Methods

### `make(string $name = ''): static`

Creates a new FiltersGroup instance with the given name.

### `schema(array $filters): static`

Sets the filters that belong to this group. Automatically applies the group name to all filters.

### `label(string $label): static`

Sets a custom label for the group.

### `hideGroupLabel(): static`

Hides the group label from display.

### `showGroupLabel(): static`

Shows the group label (default behavior).

### `getName(): string`

Returns the group name.

### `getLabel(): ?string`

Returns the group label (null if hidden).

### `isLabelHidden(): bool`

Checks if the group label should be hidden.

### `getFilters(): array`

Returns all filters in this group.

### `addFilter(BaseFilter $filter): static`

Adds a single filter to the group.

## Complete Example

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->filters([
            // Suspension Status Group (no label)
            FiltersGroup::make('suspension_status')->schema([
                TernaryFilter::make('temporary_suspended')
                    ->label('Temporarily Suspended')
                    ->query(function ($query, $value) {
                        if ($value === true) {
                            $query->whereNotNull('suspended_at')
                                  ->whereNotNull('suspended_until');
                        }
                    })
                    ->toggle(),

                TernaryFilter::make('permanently_suspended')
                    ->label('Permanently Suspended')
                    ->query(function ($query, $value) {
                        if ($value === true) {
                            $query->whereNotNull('suspended_at')
                                  ->whereNull('suspended_until');
                        }
                    })
                    ->toggle(),
            ])->hideGroupLabel(),

            // User Status Group (with custom label)
            FiltersGroup::make('user_status')
                ->label('User Status Filters')
                ->schema([
                    TernaryFilter::make('is_admin')
                        ->label('Administrator')
                        ->toggle(),

                    TernaryFilter::make('email_verified')
                        ->label('Email Verified')
                        ->toggle(),
                ]),

            // Individual filters
            SelectFilter::make('plan')
                ->label('Plan')
                ->relationship('plan', 'name'),

            SelectFilter::make('status')
                ->label('Status')
                ->options(['active' => 'Active', 'inactive' => 'Inactive']),
        ])
        ->filterColumns(4); // 4 columns total
}
```

## Comparison with Individual group() Method

### Old Approach (still supported)

```php
->filters([
    TernaryFilter::make('filter1')->group('some_name'),
    TernaryFilter::make('filter2')->group('some_name'),
    TernaryFilter::make('filter3')->group('some_name'),
    SelectFilter::make('filter4')
])
```

### New FiltersGroup Approach

```php
->filters([
    FiltersGroup::make('some_name')->schema([
        TernaryFilter::make('filter1'),
        TernaryFilter::make('filter2'),
        TernaryFilter::make('filter3'),
    ])->hideGroupLabel(),
    SelectFilter::make('filter4')
])
```

## Benefits

1. **Better Organization**: Filters are visually grouped in your code
2. **Label Control**: Easy control over group label visibility
3. **Maintainability**: Easier to manage related filters together
4. **Flexibility**: Mix FiltersGroup with individual filters
5. **Backward Compatibility**: Works alongside the existing `group()` method

## Notes

- The `FiltersGroup` automatically calls the `group()` method on all filters in its schema
- Both approaches (FiltersGroup and individual group() method) can be used together
- Group labels can be customized or hidden as needed
- The underlying filter grouping logic remains the same
