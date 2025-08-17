<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Actions;

use Illuminate\Support\HtmlString;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\BaseAction;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;
use PHPUnit\Framework\Attributes\Test;

class BaseActionTest extends BaseTest
{
    #[Test]
    public function it_can_set_modal_confirmation_button_label_as_string(): void
    {
        $action = BaseAction::make('test')->modalConfirmationButtonLabel('Delete Item');

        $this->assertEquals('Delete Item', $action->modalConfirmationButtonLabel);
    }

    #[Test]
    public function it_can_set_modal_confirmation_button_label_as_html_string(): void
    {
        $htmlLabel = new HtmlString('<strong>Delete</strong>');
        $action = BaseAction::make('test')->modalConfirmationButtonLabel($htmlLabel);

        $this->assertEquals($htmlLabel, $action->modalConfirmationButtonLabel);
    }

    #[Test]
    public function it_can_set_modal_confirmation_button_label_as_closure(): void
    {
        $closure = fn (TestModel $record) => "Delete {$record->name}";
        $action = BaseAction::make('test')->modalConfirmationButtonLabel($closure);

        $this->assertEquals($closure, $action->modalConfirmationButtonLabel);
    }

    #[Test]
    public function it_returns_null_when_no_confirmation_button_label_is_set(): void
    {
        $action = BaseAction::make('test');

        $this->assertNull($action->getModalConfirmationButtonLabel());
    }

    #[Test]
    public function it_returns_string_confirmation_button_label(): void
    {
        $action = BaseAction::make('test')->modalConfirmationButtonLabel('Delete Item');

        $this->assertEquals('Delete Item', $action->getModalConfirmationButtonLabel());
    }

    #[Test]
    public function it_returns_html_string_confirmation_button_label(): void
    {
        $htmlLabel = new HtmlString('<strong>Delete</strong>');
        $action = BaseAction::make('test')->modalConfirmationButtonLabel($htmlLabel);

        $this->assertEquals($htmlLabel, $action->getModalConfirmationButtonLabel());
    }

    #[Test]
    public function it_executes_closure_confirmation_button_label_with_record(): void
    {
        $record = new TestModel(['name' => 'Test Item']);
        $action = BaseAction::make('test')->modalConfirmationButtonLabel(fn (TestModel $model) => "Delete {$model->name}");

        $result = $action->getModalConfirmationButtonLabel($record);

        $this->assertEquals('Delete Test Item', $result);
    }

    #[Test]
    public function it_executes_closure_confirmation_button_label_without_record(): void
    {
        $action = BaseAction::make('test')->modalConfirmationButtonLabel(fn () => 'Delete Item');

        $result = $action->getModalConfirmationButtonLabel();

        $this->assertEquals('Delete Item', $result);
    }

    #[Test]
    public function confirmation_modal_content_includes_button_label(): void
    {
        $action = BaseAction::make('test')
            ->requiresConfirmation()
            ->modalConfirmationButtonLabel('Delete Item');

        $content = $action->confirmationModalContent();

        $this->assertArrayHasKey('confirmationButtonLabel', $content);
        $this->assertEquals('Delete Item', $content['confirmationButtonLabel']);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function confirmation_modal_content_includes_null_when_no_button_label_set(): void
    {
        $action = BaseAction::make('test')->requiresConfirmation();

        $content = $action->confirmationModalContent();

        $this->assertArrayHasKey('confirmationButtonLabel', $content);
        $this->assertNull($content['confirmationButtonLabel']);
    }

    #[Test]
    public function it_can_be_created_with_key(): void
    {
        $action = BaseAction::make('test-action');

        $this->assertEquals('test-action', $action->key);
        $this->assertEquals('test-action', $action->label);
    }

    #[Test]
    public function it_can_get_view(): void
    {
        $action = BaseAction::make('test');

        $this->assertEquals('laravel-simple-datatables-and-forms::components.actions.index', $action->getView());
    }

    #[Test]
    public function it_can_set_button_view(): void
    {
        $action = BaseAction::make('test')->button();

        $this->assertEquals('button', $action->actionView);
    }

    #[Test]
    public function it_can_set_icon_button_view(): void
    {
        $action = BaseAction::make('test')->iconButton();

        $this->assertEquals('icon', $action->actionView);
    }

    #[Test]
    public function it_can_set_badge_view(): void
    {
        $action = BaseAction::make('test')->badge();

        $this->assertEquals('badge', $action->actionView);
    }

    #[Test]
    public function it_can_set_icon(): void
    {
        $action = BaseAction::make('test')->icon('edit');

        $this->assertEquals('edit', $action->icon);
    }

    #[Test]
    public function it_can_set_icon_as_closure(): void
    {
        $closure = fn () => 'dynamic-icon';
        $action = BaseAction::make('test')->icon($closure);

        $this->assertEquals($closure, $action->icon);
    }

    #[Test]
    public function it_can_hide_icon(): void
    {
        $action = BaseAction::make('test')
            ->icon('edit')
            ->hiddenIcon();

        $this->assertNull($action->icon);
    }

    #[Test]
    public function it_can_set_url(): void
    {
        $action = BaseAction::make('test')->url('/test-url');

        $this->assertEquals('/test-url', $action->url);
    }

    #[Test]
    public function it_only_sets_url_if_null(): void
    {
        $action = BaseAction::make('test');
        $action->url = '/existing-url';
        $action->url('/new-url');

        $this->assertEquals('/existing-url', $action->url);
    }

    #[Test]
    public function it_can_set_color(): void
    {
        $action = BaseAction::make('test')->color('text-red-500');

        $this->assertEquals('text-red-500', $action->textColor);
        $this->assertEquals('red-500', $action->color);
    }

    #[Test]
    public function it_can_set_label(): void
    {
        $action = BaseAction::make('test')->label('Custom Label');

        $this->assertEquals('Custom Label', $action->label);
    }

    #[Test]
    public function it_can_set_label_as_array(): void
    {
        $labels = ['en' => 'English Label', 'es' => 'Spanish Label'];
        $action = BaseAction::make('test')->label($labels);

        $this->assertEquals($labels, $action->label);
    }

    #[Test]
    public function it_can_set_requires_confirmation(): void
    {
        $action = BaseAction::make('test')->requiresConfirmation();

        $this->assertTrue($action->requiresConfirmation);
    }

    #[Test]
    public function it_can_set_modal_heading(): void
    {
        $action = BaseAction::make('test')->modalHeading('Confirm Action');

        $this->assertEquals('Confirm Action', $action->modalHeading);
    }

    #[Test]
    public function it_can_set_modal_heading_as_html_string(): void
    {
        $htmlHeading = new HtmlString('<strong>Confirm</strong>');
        $action = BaseAction::make('test')->modalHeading($htmlHeading);

        $this->assertEquals($htmlHeading, $action->modalHeading);
    }

    #[Test]
    public function it_can_set_modal_heading_as_closure(): void
    {
        $closure = fn () => 'Dynamic Heading';
        $action = BaseAction::make('test')->modalHeading($closure);

        $this->assertEquals($closure, $action->modalHeading);
    }

    #[Test]
    public function it_can_set_modal_description(): void
    {
        $action = BaseAction::make('test')->modalDescription('Are you sure?');

        $this->assertEquals('Are you sure?', $action->modalDescription);
    }

    #[Test]
    public function it_can_set_modal_content(): void
    {
        $action = BaseAction::make('test')->modalContent('Custom content');

        $this->assertEquals('Custom content', $action->modalContent);
    }

    #[Test]
    public function it_can_set_modal_icon(): void
    {
        $action = BaseAction::make('test')->modalIcon('warning');

        $this->assertEquals('warning', $action->modalIcon);
    }

    #[Test]
    public function it_can_set_action_name(): void
    {
        $action = BaseAction::make('test')->action('customAction');

        $this->assertEquals('customAction', $action->actionName);
    }

    #[Test]
    public function it_can_set_action_closure(): void
    {
        $closure = fn () => 'action executed';
        $action = BaseAction::make('test')->action($closure);

        $this->assertEquals($closure, $action->actionClosure);
    }

    #[Test]
    public function it_can_set_button_background(): void
    {
        $action = BaseAction::make('test')->buttonBackground('bg-blue-500');

        $this->assertEquals('bg-blue-500', $action->buttonBackground);
    }

    #[Test]
    public function it_can_get_modal_heading(): void
    {
        $action = BaseAction::make('test')->modalHeading('Test Heading');

        $this->assertEquals('Test Heading', $action->getModalHeading());
    }

    #[Test]
    public function it_can_get_modal_heading_from_closure(): void
    {
        $record = new TestModel(['name' => 'Test Item']);
        $action = BaseAction::make('test')->modalHeading(fn ($model) => "Delete {$model->name}");

        $this->assertEquals('Delete Test Item', $action->getModalHeading($record));
    }

    #[Test]
    public function it_can_get_modal_description(): void
    {
        $action = BaseAction::make('test')->modalDescription('Test Description');

        $this->assertEquals('Test Description', $action->getModalDescription());
    }

    #[Test]
    public function it_can_get_modal_content(): void
    {
        $action = BaseAction::make('test')->modalContent('Test Content');

        $this->assertEquals('Test Content', $action->getModalContent());
    }

    #[Test]
    public function it_can_get_modal_icon(): void
    {
        $action = BaseAction::make('test')->modalIcon('warning');

        $this->assertEquals('warning', $action->getModalIcon());
    }

    #[Test]
    public function it_returns_null_for_empty_modal_properties(): void
    {
        $action = BaseAction::make('test');

        $this->assertNull($action->getModalHeading());
        $this->assertNull($action->getModalDescription());
        $this->assertNull($action->getModalContent());
        $this->assertNull($action->getModalIcon());
    }
}
