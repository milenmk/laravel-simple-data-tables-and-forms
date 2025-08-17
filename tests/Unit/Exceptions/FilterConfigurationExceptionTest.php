<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Exceptions;

use Milenmk\LaravelSimpleDatatablesAndForms\Exceptions\FilterConfigurationException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FilterConfigurationExceptionTest extends TestCase
{
    #[Test]
    public function conflicting_configuration_creates_exception_with_message(): void
    {
        $message = 'Filter configuration conflict detected';

        $exception = FilterConfigurationException::conflictingConfiguration($message);

        $this->assertEquals($message, $exception->getMessage());
    }

    #[Test]
    public function conflicting_configuration_with_empty_message(): void
    {
        $message = '';

        $exception = FilterConfigurationException::conflictingConfiguration($message);

        $this->assertEquals($message, $exception->getMessage());
    }

    #[Test]
    public function conflicting_configuration_with_complex_message(): void
    {
        $message = 'Cannot use both "options" and "query" parameters simultaneously in SelectFilter configuration';

        $exception = FilterConfigurationException::conflictingConfiguration($message);

        $this->assertEquals($message, $exception->getMessage());
    }
}
