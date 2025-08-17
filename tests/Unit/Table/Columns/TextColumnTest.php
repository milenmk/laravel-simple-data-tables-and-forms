<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Columns;

use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\TextColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use ReflectionException;

class TextColumnTest extends BaseTest
{
    protected TextColumn $column;

    protected function setUp(): void
    {
        parent::setUp();

        $this->column = new TextColumn('test_key');
    }

    #[Test]
    public function it_has_correct_default_values()
    {
        $this->assertFalse($this->column->isNumeric);
        $this->assertFalse($this->column->isDate);
        $this->assertFalse($this->column->isBadge);
        $this->assertEquals(2, $this->column->decimalPlaces);
    }

    #[Test]
    public function it_can_be_set_as_numeric()
    {
        $this->column->numeric();

        $this->assertTrue($this->column->isNumeric);
        $this->assertEquals(2, $this->column->decimalPlaces);

        $this->column->numeric(4);

        $this->assertTrue($this->column->isNumeric);
        $this->assertEquals(4, $this->column->decimalPlaces);
    }

    #[Test]
    public function it_can_be_set_as_date()
    {
        $this->column->date();

        $this->assertTrue($this->column->isDate);
    }

    #[Test]
    public function it_can_be_set_as_badge()
    {
        $this->column->badge();

        $this->assertTrue($this->column->isBadge);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function it_has_correct_view_path()
    {
        $reflectionClass = new ReflectionClass($this->column);
        $property = $reflectionClass->getProperty('view');

        $this->assertEquals(
            'laravel-simple-datatables-and-forms::components.table.columns.text',
            $property->getValue($this->column),
        );
    }
}
