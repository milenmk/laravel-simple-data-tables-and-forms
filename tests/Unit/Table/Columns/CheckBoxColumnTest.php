<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Columns;

use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\CheckBoxColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;

class CheckBoxColumnTest extends BaseTest
{
    #[Test]
    public function checkbox_column_creation(): void
    {
        $column = new CheckBoxColumn('selected');

        $this->assertEquals('selected', $column->key);
    }

    #[Test]
    public function checkbox_column_has_correct_view(): void
    {
        $column = new CheckBoxColumn('selected');

        $reflection = new ReflectionClass($column);
        $viewProperty = $reflection->getProperty('view');

        $this->assertEquals(
            'laravel-simple-datatables-and-forms::components.table.columns.checkbox',
            $viewProperty->getValue($column),
        );
    }

    #[Test]
    public function checkbox_column_inherits_column_functionality(): void
    {
        $column = new CheckBoxColumn('bulk_select');

        // Test inherited methods
        $result = $column->sortable();
        $this->assertTrue($column->sortable);
        $this->assertSame($column, $result);

        $result = $column->searchable();
        $this->assertTrue($column->searchable);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function checkbox_column_with_fluent_interface(): void
    {
        $column = new CheckBoxColumn('select_item');

        $result = $column
            ->sortable(false)
            ->searchable(false)
            ->visible();

        $this->assertFalse($column->sortable);
        $this->assertFalse($column->searchable);
        $this->assertTrue($column->visible);
        $this->assertSame($column, $result);
    }
}
