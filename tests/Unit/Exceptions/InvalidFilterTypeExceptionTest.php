<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Exceptions;

use Milenmk\LaravelSimpleDatatablesAndForms\Exceptions\InvalidFilterTypeException;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters\BaseFilter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

class InvalidFilterTypeExceptionTest extends TestCase
{
    #[Test]
    public function not_instance_of_base_filter_with_object(): void
    {
        $invalidFilter = new stdClass;

        $exception = InvalidFilterTypeException::notInstanceOfBaseFilter($invalidFilter);

        $this->assertStringContainsString('All filters must be instances of the', $exception->getMessage());
        $this->assertStringContainsString(BaseFilter::class, $exception->getMessage());
        $this->assertStringContainsString('stdClass', $exception->getMessage());
    }

    #[Test]
    public function not_instance_of_base_filter_with_string(): void
    {
        $invalidFilter = 'not a filter';

        $exception = InvalidFilterTypeException::notInstanceOfBaseFilter($invalidFilter);

        $this->assertStringContainsString('All filters must be instances of the', $exception->getMessage());
        $this->assertStringContainsString(BaseFilter::class, $exception->getMessage());
        $this->assertStringContainsString('string', $exception->getMessage());
    }

    #[Test]
    public function not_instance_of_base_filter_with_array(): void
    {
        $invalidFilter = ['not', 'a', 'filter'];

        $exception = InvalidFilterTypeException::notInstanceOfBaseFilter($invalidFilter);

        $this->assertStringContainsString('All filters must be instances of the', $exception->getMessage());
        $this->assertStringContainsString(BaseFilter::class, $exception->getMessage());
        $this->assertStringContainsString('array', $exception->getMessage());
    }

    #[Test]
    public function not_instance_of_base_filter_with_null(): void
    {
        $invalidFilter = null;

        $exception = InvalidFilterTypeException::notInstanceOfBaseFilter($invalidFilter);

        $this->assertStringContainsString('All filters must be instances of the', $exception->getMessage());
        $this->assertStringContainsString(BaseFilter::class, $exception->getMessage());
        $this->assertStringContainsString('NULL', $exception->getMessage());
    }

    #[Test]
    public function not_instance_of_base_filter_with_integer(): void
    {
        $invalidFilter = 123;

        $exception = InvalidFilterTypeException::notInstanceOfBaseFilter($invalidFilter);

        $this->assertStringContainsString('All filters must be instances of the', $exception->getMessage());
        $this->assertStringContainsString(BaseFilter::class, $exception->getMessage());
        $this->assertStringContainsString('integer', $exception->getMessage());
    }
}
