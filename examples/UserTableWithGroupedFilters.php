<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Milenmk\LaravelSimpleDatatables\Contracts\HasTableInterface;
use Milenmk\LaravelSimpleDatatables\Table\Columns\Column;
use Milenmk\LaravelSimpleDatatables\Table\Filters\SelectFilter;
use Milenmk\LaravelSimpleDatatables\Table\Filters\TernaryFilter;
use Milenmk\LaravelSimpleDatatables\Table\Table;
use Milenmk\LaravelSimpleDatatables\Traits\HasTable;

class UserTableWithGroupedFilters extends Component implements HasTableInterface
{
    use HasTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->model(User::class)
            ->heading('Users Management')
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
                Column::make('suspended_at')
                    ->label('Suspended At')
                    ->sortable(),
                Column::make('suspended_until')
                    ->label('Suspended Until')
                    ->sortable(),
                Column::make('is_admin')
                    ->label('Admin')
                    ->sortable(),
                Column::make('email_verified_at')
                    ->label('Email Verified')
                    ->sortable(),
                Column::make('plan.name')
                    ->label('Plan')
                    ->sortable(),
            ])
            ->filters([
                // Suspension Status Group - 3 filters in 1 column
                TernaryFilter::make('temporary_suspended')
                    ->label(__('Temporarily Suspended'))
                    ->group('suspension_status') // Group these filters together
                    ->query(function ($query, $value) {
                        if ($value === true || $value === 'true') {
                            $query->whereNotNull('suspended_at')->whereNotNull('suspended_until');
                        } elseif ($value === false || $value === 'false') {
                            $query->where(function ($q) {
                                $q->whereNull('suspended_at')->orWhereNull('suspended_until');
                            });
                        }
                    })
                    ->toggle(),

                TernaryFilter::make('permanently_suspended')
                    ->label(__('Permanently Suspended'))
                    ->group('suspension_status') // Same group as above
                    ->query(function ($query, $value) {
                        if ($value === true || $value === 'true') {
                            $query->whereNotNull('suspended_at')->whereNull('suspended_until');
                        } elseif ($value === false || $value === 'false') {
                            $query->where(function ($q) {
                                $q->whereNull('suspended_at')->orWhereNotNull('suspended_until');
                            });
                        }
                    })
                    ->toggle(),

                TernaryFilter::make('any_suspended')
                    ->label(__('Any Suspension'))
                    ->group('suspension_status') // Same group as above
                    ->query(function ($query, $value) {
                        if ($value === true || $value === 'true') {
                            $query->whereNotNull('suspended_at');
                        } elseif ($value === false || $value === 'false') {
                            $query->whereNull('suspended_at');
                        }
                    })
                    ->toggle(),

                // User Status Group - 2 filters in 1 column
                TernaryFilter::make('is_admin')
                    ->label(__('Administrator?'))
                    ->group('user_status') // Another group
                    ->toggle(),

                TernaryFilter::make('email_verified_at')
                    ->label(__('Unverified email'))
                    ->group('user_status') // Same group as above
                    ->query(function ($query, $value) {
                        if ($value === true || $value === 'true') {
                            $query->whereNull('email_verified_at');
                        } elseif ($value === false || $value === 'false') {
                            $query->where(function ($q) {
                                $q->whereNotNull('email_verified_at');
                            });
                        }
                    })
                    ->toggle(),

                // Standalone filters - each takes 1 column
                SelectFilter::make('plan')
                    ->label(__('Plan'))
                    ->relationship('plan', 'name'),

                SelectFilter::make('created_month')
                    ->label(__('Created Month'))
                    ->options([
                        '01' => 'January',
                        '02' => 'February',
                        '03' => 'March',
                        '04' => 'April',
                        '05' => 'May',
                        '06' => 'June',
                        '07' => 'July',
                        '08' => 'August',
                        '09' => 'September',
                        '10' => 'October',
                        '11' => 'November',
                        '12' => 'December',
                    ])
                    ->query(function ($query, $value) {
                        $query->whereMonth('created_at', $value);
                    }),
            ])
            // Set custom filter columns (4 columns total)
            // Column 1: suspension_status group (3 filters stacked)
            // Column 2: user_status group (2 filters stacked)
            // Column 3: plan filter
            // Column 4: created_month filter
            ->filterColumns(4);
    }

    public function render()
    {
        return view('livewire.user-table-with-grouped-filters', [
            'table' => $this->table,
        ]);
    }
}
