<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Columns;

use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\ProgressColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;

class ProgressColumnTest extends BaseTest
{
    #[Test]
    public function progress_column_creation(): void
    {
        $column = new ProgressColumn('completion');

        $this->assertEquals('completion', $column->key);
    }

    #[Test]
    public function progress_column_has_correct_view(): void
    {
        $column = new ProgressColumn('completion');

        $reflection = new ReflectionClass($column);
        $viewProperty = $reflection->getProperty('view');

        $this->assertEquals(
            'laravel-simple-datatables-and-forms::components.table.columns.progress',
            $viewProperty->getValue($column),
        );
    }

    #[Test]
    public function progress_column_inherits_column_functionality(): void
    {
        $column = new ProgressColumn('task_progress');

        // Test inherited methods
        $result = $column->sortable();
        $this->assertTrue($column->sortable);
        $this->assertSame($column, $result);

        $result = $column->searchable();
        $this->assertTrue($column->searchable);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function progress_column_with_fluent_interface(): void
    {
        $column = new ProgressColumn('upload_progress');

        $result = $column
            ->sortable()
            ->searchable(false)
            ->visible();

        $this->assertTrue($column->sortable);
        $this->assertFalse($column->searchable);
        $this->assertTrue($column->visible);
        $this->assertSame($column, $result);
    }
}
