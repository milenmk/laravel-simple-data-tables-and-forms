<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Actions;

use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\EditAction;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;

class EditActionTest extends BaseTest
{
    #[Test]
    public function edit_action_creation(): void
    {
        $action = new EditAction('name');

        $this->assertEquals('heroicon-o-pencil-square', $action->icon);
    }

    #[Test]
    public function edit_action_label_with_string(): void
    {
        $action = new EditAction('edit');
        $result = $action->label('Modify');

        $this->assertEquals('Modify', $action->label);
        $this->assertSame($action, $result); // Test fluent interface
    }

    #[Test]
    public function edit_action_label_with_array(): void
    {
        $action = new EditAction('edit');
        $labelArray = ['en' => 'Edit', 'es' => 'Editar'];
        $result = $action->label($labelArray);

        $this->assertEquals($labelArray, $action->label);
        $this->assertSame($action, $result);
    }

    #[Test]
    public function edit_action_label_with_null_uses_default(): void
    {
        // Mock the __() function to return a predictable value
        $action = new EditAction('edit');
        $result = $action->label(null);

        // The label should be set to the result of __('Edit')
        $this->assertNotNull($action->label);
        $this->assertSame($action, $result);
    }
}
