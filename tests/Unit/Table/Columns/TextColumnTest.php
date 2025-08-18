<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Columns;

use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\TextColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;

class TextColumnTest extends BaseTest
{
    #[Test]
    public function it_can_be_created_with_key()
    {
        $column = TextColumn::make('test_key');

        $this->assertEquals('test_key', $column->key);
        $this->assertFalse($column->isNumeric);
        $this->assertFalse($column->isDate);
        $this->assertFalse($column->isBadge);
        $this->assertEquals(2, $column->decimalPlaces);
        $this->assertEquals('Y-m-d H:i:s', $column->dateFormat);
    }

    #[Test]
    public function it_can_set_numeric_with_default_decimals()
    {
        $column = TextColumn::make('price');

        $result = $column->numeric();

        $this->assertTrue($column->isNumeric);
        $this->assertEquals(2, $column->decimalPlaces);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_set_numeric_with_custom_decimals()
    {
        $column = TextColumn::make('price');

        $result = $column->numeric(4);

        $this->assertTrue($column->isNumeric);
        $this->assertEquals(4, $column->decimalPlaces);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_set_date_with_default_format()
    {
        $column = TextColumn::make('created_at');

        $result = $column->date();

        $this->assertTrue($column->isDate);
        $this->assertEquals('Y-m-d H:i:s', $column->dateFormat);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_set_date_with_custom_format()
    {
        $column = TextColumn::make('created_at');

        $result = $column->date('d/m/Y');

        $this->assertTrue($column->isDate);
        $this->assertEquals('d/m/Y', $column->dateFormat);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_set_badge()
    {
        $column = TextColumn::make('status');

        $result = $column->badge();

        $this->assertTrue($column->isBadge);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_chain_multiple_configurations()
    {
        $column = TextColumn::make('amount')
            ->numeric(3)
            ->badge();

        $this->assertTrue($column->isNumeric);
        $this->assertEquals(3, $column->decimalPlaces);
        $this->assertTrue($column->isBadge);
        $this->assertFalse($column->isDate);
    }

    #[Test]
    public function it_has_correct_view()
    {
        $column = TextColumn::make('test');

        $this->assertEquals('laravel-simple-datatables-and-forms::components.table.columns.text', $column->getView());
    }
}
