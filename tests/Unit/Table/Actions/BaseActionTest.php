<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Actions;

use Illuminate\Support\HtmlString;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\BaseAction;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;

class BaseActionTest extends BaseTest
{
    /**
     * @test
     */
    public function it_can_set_modal_confirmation_button_label_as_string(): void
    {
        $action = BaseAction::make('test')->modalConfirmationButtonLabel('Delete Item');

        $this->assertEquals('Delete Item', $action->modalConfirmationButtonLabel);
    }

    /**
     * @test
     */
    public function it_can_set_modal_confirmation_button_label_as_html_string(): void
    {
        $htmlLabel = new HtmlString('<strong>Delete</strong>');
        $action = BaseAction::make('test')->modalConfirmationButtonLabel($htmlLabel);

        $this->assertEquals($htmlLabel, $action->modalConfirmationButtonLabel);
    }

    /**
     * @test
     */
    public function it_can_set_modal_confirmation_button_label_as_closure(): void
    {
        $closure = fn (TestModel $record) => "Delete {$record->name}";
        $action = BaseAction::make('test')->modalConfirmationButtonLabel($closure);

        $this->assertEquals($closure, $action->modalConfirmationButtonLabel);
    }

    /**
     * @test
     */
    public function it_returns_null_when_no_confirmation_button_label_is_set(): void
    {
        $action = BaseAction::make('test');

        $this->assertNull($action->getModalConfirmationButtonLabel());
    }

    /**
     * @test
     */
    public function it_returns_string_confirmation_button_label(): void
    {
        $action = BaseAction::make('test')->modalConfirmationButtonLabel('Delete Item');

        $this->assertEquals('Delete Item', $action->getModalConfirmationButtonLabel());
    }

    /**
     * @test
     */
    public function it_returns_html_string_confirmation_button_label(): void
    {
        $htmlLabel = new HtmlString('<strong>Delete</strong>');
        $action = BaseAction::make('test')->modalConfirmationButtonLabel($htmlLabel);

        $this->assertEquals($htmlLabel, $action->getModalConfirmationButtonLabel());
    }

    /**
     * @test
     */
    public function it_executes_closure_confirmation_button_label_with_record(): void
    {
        $record = new TestModel(['name' => 'Test Item']);
        $action = BaseAction::make('test')->modalConfirmationButtonLabel(fn (TestModel $model) => "Delete {$model->name}");

        $result = $action->getModalConfirmationButtonLabel($record);

        $this->assertEquals('Delete Test Item', $result);
    }

    /**
     * @test
     */
    public function it_executes_closure_confirmation_button_label_without_record(): void
    {
        $action = BaseAction::make('test')->modalConfirmationButtonLabel(fn () => 'Delete Item');

        $result = $action->getModalConfirmationButtonLabel();

        $this->assertEquals('Delete Item', $result);
    }

    /**
     * @test
     */
    public function confirmation_modal_content_includes_button_label(): void
    {
        $action = BaseAction::make('test')
            ->requiresConfirmation()
            ->modalConfirmationButtonLabel('Delete Item');

        $content = $action->confirmationModalContent();

        $this->assertArrayHasKey('confirmationButtonLabel', $content);
        $this->assertEquals('Delete Item', $content['confirmationButtonLabel']);
    }

    /**
     * @test
     */
    public function confirmation_modal_content_includes_html_button_label(): void
    {
        $htmlLabel = new HtmlString('<strong>Delete</strong>');
        $action = BaseAction::make('test')
            ->requiresConfirmation()
            ->modalConfirmationButtonLabel($htmlLabel);

        $content = $action->confirmationModalContent();

        $this->assertArrayHasKey('confirmationButtonLabel', $content);
        $this->assertEquals('<strong>Delete</strong>', $content['confirmationButtonLabel']);
    }

    /**
     * @test
     */
    public function confirmation_modal_content_includes_closure_button_label_with_record(): void
    {
        $record = new TestModel(['name' => 'Test Item']);
        $action = BaseAction::make('test')
            ->requiresConfirmation()
            ->modalConfirmationButtonLabel(fn (TestModel $model) => "Delete {$model->name}");

        $content = $action->confirmationModalContent($record);

        $this->assertArrayHasKey('confirmationButtonLabel', $content);
        $this->assertEquals('Delete Test Item', $content['confirmationButtonLabel']);
    }

    /**
     * @test
     */
    public function confirmation_modal_content_includes_null_when_no_button_label_set(): void
    {
        $action = BaseAction::make('test')->requiresConfirmation();

        $content = $action->confirmationModalContent();

        $this->assertArrayHasKey('confirmationButtonLabel', $content);
        $this->assertNull($content['confirmationButtonLabel']);
    }
}
