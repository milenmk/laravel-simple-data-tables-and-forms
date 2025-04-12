# Laravel Simple Datatables

A lightweight, easy-to-use Laravel package for creating interactive data tables with sorting, filtering, searching, and exporting capabilities.

![Screenshot](resources/img/light.png)
![Screenshot](resources/img/dark.png)

## Features

- 🔍 Advanced search with debouncing and minimum character requirements
- 🔄 Column sorting
- 🧹 Filtering with multiple filter types
- 📊 Data grouping
- 📱 Responsive design
- 🎨 Customizable appearance
- 📤 Export to CSV, Excel, and PDF
- 🔒 Security features
- ⚡ Performance optimizations with caching
- 🧩 Livewire integration

## Requirements

- PHP 8.2 or higher
- Laravel 10.x or higher
- Livewire 3.x or higher

## Installation

You can install the package via composer:

```
composer require milenmk/laravel-simple-datatables
```

## Publishing Assets

Publish the package assets:

```
php artisan simple-datatables:publish-assets
```

Optionally, you can publish the configuration file:

```
php artisan vendor:publish --tag=laravel-simple-datatables-config
```

## Including Assets

Add the following directives to your layout file:

```blade
<head>
    <!-- Other head elements -->
    @SimpleDatatablesStyle
</head>

<body>
    <!-- Your content -->
    
    <!-- Scripts -->
    @SimpleDatatablesScript
</body>
```

### Tailwind CSS Integration

As an alternative to the assets publishing command, you can copy the content from `/vendor/milenmk/laravel-simple-datatables/resources/css/package.css` to your `app.css` file. Then:

### For Tailwind 4.x

Add `@source '../../vendor/milenmk/laravel-simple-datatables/resources/views/';` in your app.css

### For Tailwind 3.x

Add `'../../vendor/milenmk/laravel-simple-datatables/resources/views/'` inside `content: []` of `tailwind.config.js`

### Editing assets

If you want to edit the view files, run `php artisan vendor:publish --tag="laravel-simple-datatables-views"`

The view files are now available in `/resources/views/vendor/laravel-simple-datatables`

To make the classes in the package view files discovered when running `npm run dev` оr `npm run build`, add:

Do not forget to update the `@source` directives in your `app.css`

## Usage

### Using a command

1. Run `php artisan make:milenmk-datatable PostList Post` where `PostList` is the name of the Livewire component that
   will be created and `Post` is the name of the model.

2. If you want to generate the columns for the model properties you add `--generate` at the end of the command

3. The command will create a component in `App\Livewire` and a view file in `resources/views/livewire`

### Manual

1. In you Livewire component add the package trait `use HasTable;`
2. Define your table fields like:

```
public function table(Table $table): Table
    {
        return $table
            ->query(Menu::query())
            ->schema([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('route_name')
                    ->label('Route')
                    ->sortable(),
                TextColumn::make('parent_menu_id')->label('Parent ID'),
                TextColumn::make('type')->label('Type'),
                TextColumn::make('icon')->label('Icon'),
                TextColumn::make('position')
                    ->label('Position')
                    ->align('center')
                    ->headerAlign('center'),
                ToggleColumn::make('is_admin_menu')->label('Is Admin Menu'),
                IconColumn::make('is_admin_menu')->boolean(),
            ])
            ->striped();
    }
```

3. In the view file of the Livewire component add `{{ $this->table }}` to render the table

## Advanced Usage

### Adding Filters

```php
use Milenmk\LaravelSimpleDatatables\Table\Filters\SelectFilter;
use Milenmk\LaravelSimpleDatatables\Table\Filters\DateFilter;

public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->heading('Users')
        ->striped()
        ->schema([
            // Columns...
        ])
        ->filters([
            SelectFilter::make('role')
                ->options([
                    'admin' => 'Admin',
                    'user' => 'User',
                ])
                ->label('Role'),
                
            DateFilter::make('created_at')
                ->label('Created Date'),
        ]);
}
```

### Grouping Data

```php
use Milenmk\LaravelSimpleDatatables\Table\Group;

public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->heading('Users')
        ->schema([
            // Columns...
        ])
        ->groups([
            Group::make('role')
                ->label('Role'),
        ]);
}
```

### Exporting Data

The export functionality is automatically included when you use the `HasTable` trait. Users can export data to CSV, Excel, or PDF formats.

### Available Columns

- TextColumn
- ToggleColumn
- CheckBoxColumn
- ProgressColumn
- IconColumn
- ActionColumn

### Column Options/Settings

- `label` (string|array|callable) - The column header label
- `value` - Custom value for the column
- `color` (Full value e.g. text-gray-500, text-primary, text-danger etc.)
- `background` (Full value e.g. bg-gray-500, bg-danger)
- `visible` (true|false, default true)
- `weight` (font weight of the text (e.g., bold, thin, medium))
- `wrap` (true|false, default true)
- `description` (string|callable)
- `align` (left|center|right, default left)
- `headerAlign` (left|center|right, default left)
- `model` (Specify custom value for the wire:model of the field. Default is the column key inside make())
- `searchable` (true|false, default false)
- `sortable` (true|false, default false)
- `format` (callable) - Format the value before display

## DISCLAIMER

This package is provided ”as is”, without warranty of any kind, either express or implied, including but not limited to
the warranties of merchantability, fitness for a particular
purpose, or noninfringement.

The author(s) make no representations or warranties regarding the accuracy, reliability or completeness of the code or
its suitability for any specific use case. It is recommended
that you thoroughly test this package in your environment before deploying it to production.

By using this package, you acknowledge and agree that the author(s) shall not be held liable for any damages, losses or
other issues arising from the use of this software.

## Contributing

You can review the source code, report bugs, or contribute to the project by visiting the GitHub repository:

[GitHub Repository](https://github.com/milenmk/laravel-simple-datatables)

Feel free to open issues or submit pull requests. Contributions are welcome!

## Documentation

- [Configuration](docs/configuration.md)
- [Search](docs/search.md)
- [Export](docs/export.md)
- [Caching](docs/caching.md)

## License

This package is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.
