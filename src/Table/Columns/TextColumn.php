<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Table\Columns;

class TextColumn extends Column
{
    public bool $isNumeric = false;
    public bool $isDate = false;
    public bool $isBadge = false;
    public int $decimalPlaces = 2;
    public string $dateFormat = 'Y-m-d H:i:s';

    protected string $view = 'laravel-simple-datatables::components.table.columns.text';

    /**
     * Set the column to be numeric with a given number of decimal places.
     */
    public function numeric(int $decimals = 2): self
    {
        $this->isNumeric = true;
        $this->decimalPlaces = $decimals;

        return $this;
    }

    /**
     * Set the column to be a date.
     */
    public function date(?string $format = null): self
    {
        if ($format) {
            $this->dateFormat = $format;
        }

        $this->isDate = true;

        return $this;
    }

    /**
     * Set the column to display as a badge.
     */
    public function badge(): self
    {
        $this->isBadge = true;

        return $this;
    }
}
