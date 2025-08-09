# Filter Grouping

Laravel Simple DataTables now supports grouping filters together, allowing you to organize related filters in a single column while maintaining a clean and organized layout.

## Overview

Filter grouping allows you to:

- Group related filters together in a single column
- Stack grouped filters vertically within their column
- Mix grouped and individual filters
- Customize the number of columns for your filter layout
- Add optional group labels for better organization

## Basic Usage

### Setting Filter Columns

You can set the number of columns for your filter layout using the `filterColumns()` method:

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->filters([
            // Your filters here
        ])
        ->filterColumns(4); // Set to 4 columns
}
```

### Grouping Filters

To group filters together, use the `group()` method on each filter you want to group:

```php
->filters([
    // Group 1: Suspension Status (3 filters in 1 column)
    TernaryFilter::make('temporary_suspended')
        ->label('Temporarily Suspended')
        ->group('suspension_status')
        ->toggle(),

    TernaryFilter::make('permanently_suspended')
        ->label('Permanently Suspended')
        ->group('suspension_status') // Same group name
        ->toggle(),

    TernaryFilter::make('any_suspended')
        ->label('Any Suspension')
        ->group('suspension_status') // Same group name
        ->toggle(),

    // Group 2: User Status (2 filters in 1 column)
    TernaryFilter::make('is_admin')
        ->label('Administrator?')
        ->group('user_status')
        ->toggle(),

    TernaryFilter::make('email_verified')
        ->label('Email Verified')
        ->group('user_status') // Same group name
        ->toggle(),

    // Individual filters (each takes 1 column)
    SelectFilter::make('plan')
        ->label('Plan')
        ->relationship('plan', 'name'),

    SelectFilter::make('status')
        ->label('Status')
        ->options(['active' => 'Active', 'inactive' => 'Inactive']),
])
```

## Configuration

### Global Configuration

You can set default filter column settings in your `config/simple-datatables.php` file:

```php
'filters' => [
    // Default number of columns for filter layout
    'columns' => 6,

    // Enable responsive filter columns
    'responsive' => true,

    // Responsive breakpoints for filter columns
    'responsive_columns' => [
        'sm' => 1,  // 1 column on small screens
        'md' => 2,  // 2 columns on medium screens
        'lg' => 4,  // 4 columns on large screens
        'xl' => 6,  // 6 columns on extra large screens
    ],
],
```

### Per-Table Configuration

You can override the global settings for individual tables:

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->filters([
            // Your filters
        ])
        ->filterColumns(4)                    // Set columns
        ->filterResponsive(true)              // Enable responsive
        ->filterResponsiveColumns([           // Custom responsive breakpoints
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            'xl' => 4,
        ]);
}
```

## Examples

### Example 1: Basic Grouping

```php
->filters([
    // Status Group
    TernaryFilter::make('is_active')
        ->label('Active')
        ->group('status')
        ->toggle(),

    TernaryFilter::make('is_verified')
        ->label('Verified')
        ->group('status')
        ->toggle(),

    // Individual filter
    SelectFilter::make('category')
        ->label('Category')
        ->options(['tech' => 'Technology', 'business' => 'Business']),
])
->filterColumns(2) // 2 columns: 1 for status group, 1 for category
```

### Example 2: Complex Grouping

```php
->filters([
    // Date Range Group
    DateFilter::make('created_from')
        ->label('Created From')
        ->group('date_range'),

    DateFilter::make('created_to')
        ->label('Created To')
        ->group('date_range'),

    // Status Group
    TernaryFilter::make('is_published')
        ->label('Published')
        ->group('status'),

    TernaryFilter::make('is_featured')
        ->label('Featured')
        ->group('status'),

    TernaryFilter::make('is_archived')
        ->label('Archived')
        ->group('status'),

    // Individual filters
    SelectFilter::make('author')
        ->label('Author')
        ->relationship('author', 'name'),

    SelectFilter::make('category')
        ->label('Category')
        ->relationship('category', 'name'),
])
->filterColumns(4) // 4 columns: date_range, status, author, category
```

## How It Works

1. **Grouping Logic**: Filters with the same `group()` value are grouped together
2. **Column Allocation**: Each group (or individual filter) takes one column
3. **Vertical Stacking**: Grouped filters are stacked vertically within their column
4. **Responsive Design**: The layout adapts to different screen sizes based on your responsive settings

## Layout Calculation

Given the example with 4 columns and the following filters:

- Group "suspension_status" (3 filters) → Column 1
- Group "user_status" (2 filters) → Column 2
- Individual "plan" filter → Column 3
- Individual "created_month" filter → Column 4

The result is a clean 4-column layout where related filters are logically grouped together.

## Best Practices

1. **Logical Grouping**: Group related filters together (e.g., date ranges, status flags, user properties)
2. **Balanced Layout**: Try to balance the number of filters in each group for a cleaner appearance
3. **Responsive Design**: Consider how your grouped filters will look on mobile devices
4. **Group Names**: Use descriptive group names that make sense for your application
5. **Column Count**: Choose a column count that works well with your total number of filter groups

## Responsive Behavior

The filter layout automatically adapts to different screen sizes:

- **Small screens (sm)**: Typically 1 column (all groups stack vertically)
- **Medium screens (md)**: 2 columns
- **Large screens (lg)**: 4 columns
- **Extra large screens (xl)**: 6 columns (or your custom setting)

This ensures your filters remain usable and well-organized across all device types.
