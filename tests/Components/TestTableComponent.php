<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Components;

use Illuminate\Support\Facades\View;
use Livewire\Component;
use Milenmk\LaravelSimpleDatatablesAndForms\Contracts\HasTableInterface;
use Milenmk\LaravelSimpleDatatablesAndForms\Exceptions\FilterConfigurationException;
use Milenmk\LaravelSimpleDatatablesAndForms\Exceptions\InvalidFilterTypeException;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\Column;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters\SelectFilter;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters\TernaryFilter;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\HasTable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class TestTableComponent extends Component implements HasTableInterface
{
    use HasTable;

    /**
     * @throws FilterConfigurationException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws InvalidFilterTypeException
     */
    public function render()
    {
        return View::file(__DIR__ . '/../views/test/test-table-component.blade.php', [
            'table' => $this->getTableProperty(),
        ]);
    }

    /**
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(TestModel::query())
            ->model(TestModel::class)
            ->heading('Test Table')
            ->schema([
                Column::make('id')
                    ->label('ID')
                    ->sortable(),
                Column::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Column::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Column::make('category')
                    ->label('Category')
                    ->sortable(),
                Column::make('is_active')
                    ->label('Active')
                    ->sortable(),
                Column::make('price')
                    ->label('Price')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Category')
                    ->options([
                        'electronics' => 'Electronics',
                        'books' => 'Books',
                        'clothing' => 'Clothing',
                        'home' => 'Home & Garden',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->toggle(),
            ]);
    }
}
