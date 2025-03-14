## About

This package provides simple Table component to create Datatables for Livewire components

![Screenshot](resources/img/Screenshot.png)

## Requirements

- PHP 8.2 or higher
- Laravel 10.x or higher
- Livewire 3.x or higher

## Install

Run ```composer require milenmk/laravel-simple-datatables``` to install the package

If you want to edit the view files, then run ```php artisan vendor:publish --tag="laravel-simple-datatables-views"

The view files are now available in `/resources/views/vendor/laravel-simple-datatables`

## Usage

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
                CheckBoxColumn::make('is_admin_menu')->label('Is Admin Menu'),
            ])
            ->striped();
    }
```

3. In the view file of the Livewire component add `{{ $this->table }}` to render the table

## Additional Information

- Available columns: TextColumn, ToggleColumn, CheckBoxColumn and ProgressColumn
- Available column options/settings:
    - label (string|array|callable)
    - value
    - color (Full value e.g. text-gray-500, text-primary, text-danger etc.)
    - background (Full value e.g. bg-gray-500, bg-danger)
    - visible (true|false, default true)
    - weight (font weight of the text (e.g., bold, thin, medium))
    - wrap (true|false, default true)
    - description (string|callable)
    - align (left|center|right, default left)
    - model (Specify custom value for the wire:model of the field. Default is the column key inside make())

## DISCLAIMER

This package is provided ”as is”, without warranty of any kind, either express or implied, including but not limited to the warranties of merchantability, fitness for a particular
purpose, or noninfringement.

The author(s) make no representations or warranties regarding the accuracy, reliability or completeness of the code or its suitability for any specific use case. It is recommended
that you thoroughly test this package in your environment before deploying it to production.

By using this package, you acknowledge and agree that the author(s) shall not be held liable for any damages, losses or other issues arising from the use of this software.

## Contributing

You can review the source code, report bugs, or contribute to the project by visiting the GitHub repository:

[GitHub Repository](https://github.com/milenmk/laravel-simple-datatables)

Feel free to open issues or submit pull requests. Contributions are welcome!

## License

This package is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.
