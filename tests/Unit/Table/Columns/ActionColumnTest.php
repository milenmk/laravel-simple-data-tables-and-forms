<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Columns;

use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\DeleteAction;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\EditAction;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\ActionColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use ReflectionException;

class ActionColumnTest extends BaseTest
{
    private ActionColumn $column;

    protected function setUp(): void
    {
        parent::setUp();

        $this->column = ActionColumn::make('actions');
    }

    #[Test]
    public function it_can_be_created()
    {
        $column = ActionColumn::make('actions');

        $this->assertEquals('actions', $column->key);
        $this->assertFalse($column->groupActions);
        $this->assertEquals([], $column->getActions());
    }

    #[Test]
    public function it_can_set_actions()
    {
        $actions = [EditAction::make('edit'), DeleteAction::make('delete')];

        $this->column->actions($actions);

        $this->assertEquals($actions, $this->column->getActions());
    }

    #[Test]
    public function it_can_get_actions()
    {
        $actions = [EditAction::make('edit'), DeleteAction::make('delete')];

        $this->column->actions($actions);

        $retrievedActions = $this->column->getActions();

        $this->assertCount(2, $retrievedActions);
        $this->assertEquals($actions, $retrievedActions);
    }

    #[Test]
    public function it_can_set_group_actions()
    {
        $this->assertFalse($this->column->groupActions);

        $this->column->groupActions();

        $this->assertTrue($this->column->groupActions);
    }

    #[Test]
    public function it_returns_fluent_interface()
    {
        $result = $this->column->actions([]);

        $this->assertSame($this->column, $result);

        $result = $this->column->groupActions();

        $this->assertSame($this->column, $result);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function it_has_correct_view()
    {
        // Use reflection to access protected property
        $reflection = new ReflectionClass($this->column);
        $property = $reflection->getProperty('view');

        $this->assertEquals(
            'laravel-simple-datatables-and-forms::components.table.columns.action',
            $property->getValue($this->column),
        );
    }

    #[Test]
    public function it_can_set_empty_actions_array()
    {
        $this->column->actions([]);

        $this->assertEquals([], $this->column->getActions());
    }

    #[Test]
    public function it_can_override_actions()
    {
        $firstActions = [EditAction::make('edit')];
        $secondActions = [DeleteAction::make('delete')];

        $this->column->actions($firstActions);
        $this->assertEquals($firstActions, $this->column->getActions());

        $this->column->actions($secondActions);
        $this->assertEquals($secondActions, $this->column->getActions());
    }
}
