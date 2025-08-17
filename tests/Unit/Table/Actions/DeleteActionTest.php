<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Actions;

use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\DeleteAction;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;

class DeleteActionTest extends BaseTest
{
    #[Test]
    public function delete_action_creation(): void
    {
        $action = new DeleteAction('name');

        $this->assertEquals('heroicon-o-trash', $action->icon);
    }

    #[Test]
    public function delete_action_label_with_string(): void
    {
        $action = new DeleteAction('name');
        $result = $action->label('Remove');

        $this->assertEquals('Remove', $action->label);
        $this->assertSame($action, $result); // Test fluent interface
    }

    #[Test]
    public function delete_action_label_with_array(): void
    {
        $action = new DeleteAction('name');
        $labelArray = ['en' => 'Delete', 'de' => 'Löschen'];
        $result = $action->label($labelArray);

        $this->assertEquals($labelArray, $action->label);
        $this->assertSame($action, $result);
    }

    #[Test]
    public function delete_action_label_with_null_uses_default(): void
    {
        $action = new DeleteAction('name');
        $result = $action->label(null);

        // The label should be set to the result of __('Delete')
        $this->assertNotNull($action->label);
        $this->assertSame($action, $result);
    }
}
