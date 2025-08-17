<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Columns;

use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\ToggleColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use ReflectionClass;

class ToggleColumnTest extends BaseTest
{
    /**
     * @test
     */
    public function toggle_column_creation(): void
    {
        $column = new ToggleColumn('active');

        $this->assertEquals('active', $column->key);
    }

    /**
     * @test
     */
    public function toggle_column_has_correct_view(): void
    {
        $column = new ToggleColumn('active');

        $reflection = new ReflectionClass($column);
        $viewProperty = $reflection->getProperty('view');

        $this->assertEquals(
            'laravel-simple-datatables-and-forms::components.table.columns.toggle',
            $viewProperty->getValue($column),
        );
    }

    /**
     * @test
     */
    public function toggle_column_inherits_column_functionality(): void
    {
        $column = new ToggleColumn('is_enabled');

        // Test inherited methods
        $result = $column->sortable();
        $this->assertTrue($column->sortable);
        $this->assertSame($column, $result);

        $result = $column->searchable();
        $this->assertTrue($column->searchable);
        $this->assertSame($column, $result);
    }

    /**
     * @test
     */
    public function toggle_column_with_fluent_interface(): void
    {
        $column = new ToggleColumn('notifications');

        $result = $column
            ->sortable()
            ->searchable()
            ->visible(false);

        $this->assertTrue($column->sortable);
        $this->assertTrue($column->searchable);
        $this->assertFalse($column->visible);
        $this->assertSame($column, $result);
    }
}
