<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Actions;

use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\ViewAction;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;

class ViewActionTest extends BaseTest
{
    /**
     * @test
     */
    public function view_action_creation(): void
    {
        $action = new ViewAction('view');

        $this->assertEquals('heroicon-o-eye', $action->icon);
    }

    /**
     * @test
     */
    public function view_action_label_with_string(): void
    {
        $action = new ViewAction('view');
        $result = $action->label('Show');

        $this->assertEquals('Show', $action->label);
        $this->assertSame($action, $result); // Test fluent interface
    }

    /**
     * @test
     */
    public function view_action_label_with_array(): void
    {
        $action = new ViewAction('view');
        $labelArray = ['en' => 'View', 'fr' => 'Voir'];
        $result = $action->label($labelArray);

        $this->assertEquals($labelArray, $action->label);
        $this->assertSame($action, $result);
    }

    /**
     * @test
     */
    public function view_action_label_with_null_uses_default(): void
    {
        $action = new ViewAction('view');
        $result = $action->label(null);

        // The label should be set to the result of __('View')
        $this->assertNotNull($action->label);
        $this->assertSame($action, $result);
    }
}
