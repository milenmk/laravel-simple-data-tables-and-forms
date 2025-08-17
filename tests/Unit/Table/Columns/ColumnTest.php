<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Columns;

use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\Column;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;
use PHPUnit\Framework\Attributes\Test;

class ColumnTest extends BaseTest
{
    protected Column $column;

    protected function setUp(): void
    {
        parent::setUp();

        $this->column = new Column('test_key');
    }

    #[Test]
    public function it_can_be_instantiated_with_key()
    {
        $this->assertSame('test_key', $this->column->key);
        $this->assertSame('test_key', $this->column->label);
        $this->assertSame('test_key', $this->column->wireModel);
    }

    #[Test]
    public function it_can_be_created_using_make_method()
    {
        $column = Column::make('another_key');

        $this->assertSame('another_key', $column->key);
    }

    #[Test]
    public function it_can_set_and_get_sortable()
    {
        $this->assertFalse($this->column->sortable);

        $this->column->sortable();

        $this->assertTrue($this->column->sortable);

        $this->column->sortable(false);

        $this->assertFalse($this->column->sortable);
    }

    #[Test]
    public function it_can_set_and_get_searchable()
    {
        $this->assertFalse($this->column->searchable);

        $this->column->searchable();

        $this->assertTrue($this->column->searchable);

        $this->column->searchable(false);

        $this->assertFalse($this->column->searchable);
    }

    #[Test]
    public function it_can_set_and_get_label()
    {
        $label = 'Test Label';

        $this->column->label($label);

        $this->assertSame($label, $this->column->label);
    }

    #[Test]
    public function it_can_set_and_get_value()
    {
        $value = 'Test Value';

        $this->column->value($value);

        $this->assertSame($value, $this->column->value);
    }

    #[Test]
    public function it_can_get_value_from_item()
    {
        $model = new TestModel;
        $model->name = 'Test Name';

        $column = new Column('name');

        $this->assertSame('Test Name', $column->getValue($model));
    }

    #[Test]
    public function it_can_get_value_from_callable()
    {
        $model = new TestModel;
        $model->name = 'Test Name';

        $this->column->value(function ($item) {
            return 'Prefix: ' . $item->name;
        });

        $this->assertSame('Prefix: Test Name', $this->column->getValue($model));
    }

    #[Test]
    public function it_can_set_and_get_color()
    {
        $color = 'text-primary';

        $this->column->color($color);

        $this->assertSame($color, $this->column->textColor);
        $this->assertSame('primary', $this->column->color);
    }

    #[Test]
    public function it_can_set_and_get_background()
    {
        $background = 'bg-primary';

        $this->column->background($background);

        $this->assertSame($background, $this->column->backgroundColor);
    }

    #[Test]
    public function it_can_set_and_get_visibility()
    {
        $this->assertTrue($this->column->visible);

        $this->column->visible(false);

        $this->assertFalse($this->column->visible);
        $this->assertFalse($this->column->getVisibility());

        $this->column->visible();

        $this->assertTrue($this->column->visible);
        $this->assertTrue($this->column->getVisibility());
    }

    #[Test]
    public function it_can_set_and_get_weight()
    {
        $weight = 'bold';

        $this->column->weight($weight);

        $this->assertSame($weight, $this->column->weight);
    }

    #[Test]
    public function it_can_set_and_get_wrap()
    {
        $this->assertNull($this->column->wrap);

        $this->column->wrap();

        $this->assertSame('text-wrap', $this->column->wrap);

        $this->column->wrap(false);

        $this->assertSame('text-nowrap', $this->column->wrap);
    }

    #[Test]
    public function it_can_set_and_get_description()
    {
        $description = 'Test Description';

        $this->column->description($description);

        $this->assertSame($description, $this->column->description);
    }

    #[Test]
    public function it_can_get_description_from_callable()
    {
        $model = new TestModel;
        $model->name = 'Test Name';

        $this->column->description(function ($item) {
            return 'Description for: ' . $item->name;
        });

        $this->assertSame('Description for: Test Name', $this->column->getDescription($model));
    }

    #[Test]
    public function it_can_set_and_get_align()
    {
        $this->assertSame('left', $this->column->align);

        $this->column->align('center');

        $this->assertSame('center', $this->column->align);

        $this->column->align('right');

        $this->assertSame('right', $this->column->align);
    }

    #[Test]
    public function it_can_set_and_get_header_align()
    {
        $this->assertSame('left', $this->column->headerAlign);

        $this->column->headerAlign('center');

        $this->assertSame('center', $this->column->headerAlign);

        $this->column->headerAlign('right');

        $this->assertSame('right', $this->column->headerAlign);
    }

    #[Test]
    public function it_can_set_and_get_wire_model()
    {
        $wireModel = 'custom.model';

        $this->column->model($wireModel);

        $this->assertSame($wireModel, $this->column->wireModel);
    }
}
