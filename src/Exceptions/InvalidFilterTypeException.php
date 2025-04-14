<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Exceptions;

use Exception;
use Milenmk\LaravelSimpleDatatables\Table\Filters\BaseFilter;

class InvalidFilterTypeException extends Exception
{
    /**
     * Create a new exception for invalid filter type.
     *
     * @return static
     */
    public static function notInstanceOfBaseFilter(mixed $filter): self
    {
        $type = is_object($filter) ? get_class($filter) : gettype($filter);

        return new self(sprintf('All filters must be instances of the %s class. Got: %s', BaseFilter::class, $type));
    }
}
