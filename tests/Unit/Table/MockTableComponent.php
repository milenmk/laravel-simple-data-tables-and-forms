<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table;

use Illuminate\View\View;
use Livewire\Component;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\TextColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\HasTable;

class MockTableComponent extends Component
{
    use HasTable;

    public function render(): View
    {
        return view()->file(__DIR__ . '/views/mock-component.blade.php');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(TestModel::query()->paginate(10))
            ->schema([TextColumn::make('name')->label('Name'), TextColumn::make('email')->label('Email')])
            ->heading('Test Table');
    }
}
