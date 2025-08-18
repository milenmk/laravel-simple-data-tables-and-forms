<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Columns;

use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\Column;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use ReflectionException;

class ColumnTest extends BaseTest
{
    #[Test]
    public function it_can_be_created_with_key()
    {
        $column = Column::make('test_key');

        $this->assertEquals('test_key', $column->key);
        $this->assertEquals('test_key', $column->label);
        $this->assertEquals('test_key', $column->wireModel);
        $this->assertFalse($column->sortable);
        $this->assertFalse($column->searchable);
        $this->assertTrue($column->visible);
        $this->assertEquals('left', $column->align);
        $this->assertEquals('left', $column->headerAlign);
    }

    #[Test]
    public function it_can_set_sortable()
    {
        $column = Column::make('test');

        // Test enabling sortable
        $result = $column->sortable();
        $this->assertTrue($column->sortable);
        $this->assertSame($column, $result);

        // Test disabling sortable
        $column->sortable(false);
        $this->assertFalse($column->sortable);
    }

    #[Test]
    public function it_can_set_searchable_as_boolean()
    {
        $column = Column::make('test');

        // Test enabling searchable
        $result = $column->searchable();
        $this->assertTrue($column->searchable);
        $this->assertSame($column, $result);

        // Test disabling searchable
        $column->searchable(false);
        $this->assertFalse($column->searchable);
    }

    #[Test]
    public function it_can_set_searchable_as_array()
    {
        $column = Column::make('test');
        $searchFields = ['field1', 'field2'];

        $result = $column->searchable($searchFields);

        $this->assertTrue($column->searchable);
        $this->assertEquals($searchFields, $column->searchFields);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_set_label_as_string()
    {
        $column = Column::make('test');
        $result = $column->label('Custom Label');

        $this->assertEquals('Custom Label', $column->label);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_set_label_as_array()
    {
        $column = Column::make('test');
        $labels = ['en' => 'English', 'es' => 'Spanish'];

        $result = $column->label($labels);

        $this->assertEquals($labels, $column->label);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_hide_label()
    {
        $column = Column::make('test');
        $column->label('Original Label');

        $result = $column->hiddenLabel();

        $this->assertNull($column->label);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_set_value()
    {
        $column = Column::make('test');
        $result = $column->value('test_value');

        $this->assertEquals('test_value', $column->value);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_only_sets_value_if_null()
    {
        $column = Column::make('test');
        $column->value('first_value');
        $column->value('second_value');

        // Should still be the first value
        $this->assertEquals('first_value', $column->value);
    }

    #[Test]
    public function it_can_get_value_from_item()
    {
        $column = Column::make('name');
        $item = (object) ['name' => 'John Doe'];

        $value = $column->getValue($item);

        $this->assertEquals('John Doe', $value);
    }

    #[Test]
    public function it_returns_null_for_missing_property()
    {
        $column = Column::make('missing_field');
        $item = (object) ['name' => 'John Doe'];

        $value = $column->getValue($item);

        $this->assertNull($value);
    }

    #[Test]
    public function it_can_set_visible_as_boolean()
    {
        $column = Column::make('test');

        $result = $column->visible(false);
        $this->assertFalse($column->visible);
        $this->assertSame($column, $result);

        $column->visible();
        $this->assertTrue($column->visible);
    }

    #[Test]
    public function it_can_set_visible_as_callable()
    {
        $column = Column::make('test');

        $result = $column->visible(fn () => false);

        $this->assertFalse($column->getVisibility());
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_set_description()
    {
        $column = Column::make('test');
        $result = $column->description('Test description');

        $this->assertEquals('Test description', $column->description);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_set_weight()
    {
        $column = Column::make('test');
        $result = $column->weight('bold');

        $this->assertEquals('bold', $column->weight);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_set_wrap()
    {
        $column = Column::make('test');
        $result = $column->wrap();

        $this->assertEquals('text-wrap', $column->wrap);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_set_background_color()
    {
        $column = Column::make('test');
        $result = $column->background('red');

        $this->assertEquals('red', $column->backgroundColor);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_set_text_color()
    {
        $column = Column::make('test');
        $result = $column->color('blue');

        $this->assertEquals('blue', $column->textColor);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_set_color()
    {
        $column = Column::make('test');
        $result = $column->color('green');

        $this->assertEquals('green', $column->color);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_set_align()
    {
        $column = Column::make('test');
        $result = $column->align('center');

        $this->assertEquals('center', $column->align);
        $this->assertSame($column, $result);
    }

    #[Test]
    public function it_can_set_header_align()
    {
        $column = Column::make('test');
        $result = $column->headerAlign('right');

        $this->assertEquals('right', $column->headerAlign);
        $this->assertSame($column, $result);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function it_can_get_view()
    {
        $column = Column::make('test');

        // Access the protected view property using reflection
        $reflection = new ReflectionClass($column);
        $viewProperty = $reflection->getProperty('view');
        $viewProperty->setValue($column, 'test.view');

        $this->assertEquals('test.view', $column->getView());
    }
}
