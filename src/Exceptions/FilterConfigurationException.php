<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Exceptions;

use Exception;

class FilterConfigurationException extends Exception
{
    /**
     * Create a new exception for conflicting filter configurations.
     *
     * @return static
     */
    public static function conflictingConfiguration(string $message): self
    {
        return new self($message);
    }
}
