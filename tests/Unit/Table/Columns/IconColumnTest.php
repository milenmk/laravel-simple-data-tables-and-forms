<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Columns;

use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\IconColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\IconColumn\IconColumnSize;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use ReflectionClass;

class IconColumnTest extends BaseTest
{
    /**
     * @test
     */
    public function icon_column_creation(): void
    {
        $column = new IconColumn('status');

        $this->assertEquals('status', $column->key);
    }

    /**
     * @test
     */
    public function icon_column_has_correct_view(): void
    {
        $column = new IconColumn('status');

        $reflection = new ReflectionClass($column);
        $viewProperty = $reflection->getProperty('view');

        $this->assertEquals(
            'laravel-simple-datatables-and-forms::components.table.columns.icon-column',
            $viewProperty->getValue($column),
        );
    }

    /**
     * @test
     */
    public function icon_column_icon_configuration(): void
    {
        $column = new IconColumn('status');
        $result = $column->icon('heroicon-o-star');

        $reflection = new ReflectionClass($column);
        $iconProperty = $reflection->getProperty('icon');

        $this->assertEquals('heroicon-o-star', $iconProperty->getValue($column));
        $this->assertSame($column, $result); // Test fluent interface
    }

    /**
     * @test
     */
    public function icon_column_boolean_configuration(): void
    {
        $column = new IconColumn('active');
        $result = $column->boolean();

        $reflection = new ReflectionClass($column);
        $isBooleanProperty = $reflection->getProperty('isBoolean');

        $this->assertTrue($isBooleanProperty->getValue($column));
        $this->assertSame($column, $result);
    }

    /**
     * @test
     */
    public function icon_column_true_icon_configuration(): void
    {
        $column = new IconColumn('active');
        $result = $column->trueIcon('heroicon-o-check');

        $this->assertEquals('heroicon-o-check', $column->getTrueIcon());
        $this->assertSame($column, $result);
    }

    /**
     * @test
     */
    public function icon_column_false_icon_configuration(): void
    {
        $column = new IconColumn('active');
        $result = $column->falseIcon('heroicon-o-x');

        $this->assertEquals('heroicon-o-x', $column->getFalseIcon());
        $this->assertSame($column, $result);
    }

    /**
     * @test
     */
    public function icon_column_true_color_configuration(): void
    {
        $column = new IconColumn('active');
        $result = $column->trueColor('green');

        $this->assertEquals('green', $column->getTrueColor());
        $this->assertSame($column, $result);
    }

    /**
     * @test
     */
    public function icon_column_false_color_configuration(): void
    {
        $column = new IconColumn('active');
        $result = $column->falseColor('red');

        $this->assertEquals('red', $column->getFalseColor());
        $this->assertSame($column, $result);
    }

    /**
     * @test
     */
    public function icon_column_true_helper_method(): void
    {
        $column = new IconColumn('active');
        $result = $column->true('heroicon-o-check', 'green');

        $this->assertEquals('heroicon-o-check', $column->getTrueIcon());
        $this->assertEquals('green', $column->getTrueColor());
        $this->assertSame($column, $result);
    }

    /**
     * @test
     */
    public function icon_column_false_helper_method(): void
    {
        $column = new IconColumn('active');
        $result = $column->false('heroicon-o-x', 'red');

        $this->assertEquals('heroicon-o-x', $column->getFalseIcon());
        $this->assertEquals('red', $column->getFalseColor());
        $this->assertSame($column, $result);
    }

    /**
     * @test
     */
    public function icon_column_size_configuration(): void
    {
        $column = new IconColumn('status');
        $result = $column->size(IconColumnSize::Large);

        $reflection = new ReflectionClass($column);
        $sizeProperty = $reflection->getProperty('size');

        $this->assertEquals(IconColumnSize::Large, $sizeProperty->getValue($column));
        $this->assertSame($column, $result);
    }

    /**
     * @test
     */
    public function icon_column_get_icon_for_boolean_true(): void
    {
        $column = new IconColumn('active');
        $column
            ->boolean()
            ->trueIcon('heroicon-o-check')
            ->falseIcon('heroicon-o-x');

        $icon = $column->getIcon(true);
        $this->assertEquals('heroicon-o-check', $icon);
    }

    /**
     * @test
     */
    public function icon_column_get_icon_for_boolean_false(): void
    {
        $column = new IconColumn('active');
        $column
            ->boolean()
            ->trueIcon('heroicon-o-check')
            ->falseIcon('heroicon-o-x');

        $icon = $column->getIcon(false);
        $this->assertEquals('heroicon-o-x', $icon);
    }

    /**
     * @test
     */
    public function icon_column_get_icon_for_null_value(): void
    {
        $column = new IconColumn('active');
        $column->boolean();

        $icon = $column->getIcon(null);
        $this->assertNull($icon);
    }

    /**
     * @test
     */
    public function icon_column_get_icon_when_not_boolean(): void
    {
        $column = new IconColumn('status');
        // Don't set as boolean

        $icon = $column->getIcon(true);
        $this->assertNull($icon);
    }

    /**
     * @test
     */
    public function icon_column_get_color_for_boolean_true(): void
    {
        $column = new IconColumn('active');
        $column
            ->boolean()
            ->trueColor('green')
            ->falseColor('red');

        $color = $column->getColor(true);
        $this->assertEquals('green', $color);
    }

    /**
     * @test
     */
    public function icon_column_get_color_for_boolean_false(): void
    {
        $column = new IconColumn('active');
        $column
            ->boolean()
            ->trueColor('green')
            ->falseColor('red');

        $color = $column->getColor(false);
        $this->assertEquals('red', $color);
    }

    /**
     * @test
     */
    public function icon_column_get_color_for_null_value(): void
    {
        $column = new IconColumn('active');
        $column->boolean();

        $color = $column->getColor(null);
        $this->assertNull($color);
    }

    /**
     * @test
     */
    public function icon_column_get_color_when_not_boolean(): void
    {
        $column = new IconColumn('status');
        // Don't set as boolean

        $color = $column->getColor(true);
        $this->assertNull($color);
    }

    /**
     * @test
     */
    public function icon_column_default_true_icon(): void
    {
        $column = new IconColumn('active');
        $column->boolean();

        $this->assertEquals('heroicon-o-check-circle', $column->getTrueIcon());
    }

    /**
     * @test
     */
    public function icon_column_default_false_icon(): void
    {
        $column = new IconColumn('active');
        $column->boolean();

        $this->assertEquals('heroicon-o-x-circle', $column->getFalseIcon());
    }

    /**
     * @test
     */
    public function icon_column_default_true_color(): void
    {
        $column = new IconColumn('active');
        $column->boolean();

        $this->assertEquals('success', $column->getTrueColor());
    }

    /**
     * @test
     */
    public function icon_column_default_false_color(): void
    {
        $column = new IconColumn('active');
        $column->boolean();

        $this->assertEquals('danger', $column->getFalseColor());
    }

    /**
     * @test
     */
    public function icon_column_fluent_interface(): void
    {
        $column = new IconColumn('status');

        $result = $column
            ->boolean()
            ->true('heroicon-o-check', 'success')
            ->false('heroicon-o-x', 'danger')
            ->size(IconColumnSize::Medium);

        $this->assertEquals('heroicon-o-check', $column->getTrueIcon());
        $this->assertEquals('success', $column->getTrueColor());
        $this->assertEquals('heroicon-o-x', $column->getFalseIcon());
        $this->assertEquals('danger', $column->getFalseColor());
        $this->assertSame($column, $result);
    }

    /**
     * @test
     */
    public function icon_column_inherits_column_functionality(): void
    {
        $column = new IconColumn('status');

        // Test inherited methods
        $result = $column->sortable();
        $this->assertTrue($column->sortable);
        $this->assertSame($column, $result);

        $result = $column->searchable();
        $this->assertTrue($column->searchable);
        $this->assertSame($column, $result);
    }
}
