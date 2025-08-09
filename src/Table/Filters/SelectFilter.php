<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Table\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View as FacadesView;
use Illuminate\View\View;
use Milenmk\LaravelSimpleDatatables\Exceptions\FilterConfigurationException;

class SelectFilter extends BaseFilter
{
    public ?Closure $optionsCallback = null;
    public ?string $relation = null;

    protected ?string $displayColumn = null;

    /**
     * @throws FilterConfigurationException
     */
    public function options(Closure|array|string $options): static
    {
        if ($this->relation) {
            throw FilterConfigurationException::conflictingConfiguration('Cannot set both relation() and options()');
        }

        if (is_string($options) && enum_exists($options)) {
            return $this->enumOptions($options);
        }

        if (is_callable($options)) {
            $this->optionsCallback = $options;
        } else {
            $this->optionsCallback = function () use ($options) {
                return $options;
            };
        }

        return $this;
    }

    /**
     * @throws FilterConfigurationException
     */
    public function relationship(string $relation, string $displayColumn): static
    {
        if ($this->optionsCallback) {
            throw FilterConfigurationException::conflictingConfiguration('Cannot set both relation() and options()');
        }

        $this->relation = $relation;
        $this->displayColumn = $displayColumn;

        return $this;
    }

    public function apply($query, $value): void
    {
        if (empty($value)) {
            return;
        }

        if ($this->relation && $this->displayColumn) {
            $query->whereHas($this->relation, function (Builder $q) use ($value) {
                $q->whereIn('id', (array) $value);
            });
        } elseif ($this->query instanceof Closure) {
            call_user_func($this->query, $query, (array) $value);
        } else {
            $query->whereIn($this->name, (array) $value);
        }
    }

    public function render(): View
    {
        return FacadesView::make('laravel-simple-datatables::components.table.filters.select', ['filter' => $this]);
    }

    public function getOptions(): array
    {
        if ($this->optionsCallback) {
            return call_user_func($this->optionsCallback);
        }

        if ($this->relation && $this->displayColumn) {
            $modelClass = app($this->getModel())
                ->{$this->relation}()
                ->getRelated();

            return $modelClass->pluck($this->displayColumn, 'id')->toArray();
        }

        // Get all available values from the model property
        $modelClass = $this->getModel();
        $tableName = (new $modelClass)->getTable();
        $columnName = $this->name;

        return DB::table($tableName)
            ->distinct()
            ->orderBy($columnName)
            ->pluck($columnName, $columnName)
            ->toArray();
    }

    protected function enumOptions(string $enumClass): static
    {
        $this->optionsCallback = function () use ($enumClass) {
            return collect($enumClass::cases())
                ->mapWithKeys(
                    fn ($case) => [
                        $case->value => method_exists($case, 'getLabel') ? $case->getLabel() : $case->name,
                    ],
                )
                ->toArray();
        };

        return $this;
    }

    protected function getModel(): string
    {
        return $this->modelClass;
    }
}
