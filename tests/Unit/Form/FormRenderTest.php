<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\CheckboxField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\SelectField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Form;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Sections\Section;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class FormRenderTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock the Livewire variable
        View::share(
            '__livewire',
            (object) [
                'id' => 'mock-id',
                'fingerprint' => ['id' => 'mock-id', 'name' => 'mock-name', 'path' => 'mock-path'],
                'effects' => ['listeners' => []],
                'properties' => [],
            ],
        );

        // Mock the wire directive
        Blade::directive('wire', function ($expression) {
            return "<?php echo 'wire:' . {$expression}; ?>";
        });

        // Mock the entangle function
        Blade::directive('entangle', function () {
            return "<?php echo ''; ?>";
        });
    }

    #[Test]
    public function it_can_render_basic_form()
    {
        $form = new Form;

        $form->schema([
            InputField::make('name')
                ->label('Full Name')
                ->placeholder('Enter your name')
                ->required(),
            InputField::make('email')
                ->label('Email Address')
                ->email()
                ->required(),
            SelectField::make('category')
                ->label('Category')
                ->options([
                    'electronics' => 'Electronics',
                    'clothing' => 'Clothing',
                ])
                ->required(),
        ]);

        $renderedForm = $form->render();

        $this->assertStringContainsString('Full Name', $renderedForm);
        $this->assertStringContainsString('Email Address', $renderedForm);
        $this->assertStringContainsString('Category', $renderedForm);
        $this->assertStringContainsString('Electronics', $renderedForm);
        $this->assertStringContainsString('Clothing', $renderedForm);
        $this->assertStringContainsString('wire:model.defer="formData.name"', $renderedForm);
        $this->assertStringContainsString('wire:model.defer="formData.email"', $renderedForm);
        $this->assertStringContainsString('type="email"', $renderedForm);
    }

    #[Test]
    public function it_can_render_form_with_heading()
    {
        $form = new Form;

        $form->heading([
            'title' => 'User Registration',
            'description' => 'Create a new user account',
        ]);

        $form->schema([InputField::make('name')->label('Name')]);

        $renderedForm = $form->render();

        $this->assertStringContainsString('User Registration', $renderedForm);
        $this->assertStringContainsString('Create a new user account', $renderedForm);
        $this->assertStringContainsString('form-heading', $renderedForm);
    }

    #[Test]
    public function it_can_render_form_with_sections()
    {
        $form = new Form;

        $form->sections([
            Section::make('personal_info')
                ->label('Personal Information')
                ->description('Basic personal details')
                ->icon('user')
                ->fields([
                    InputField::make('name')
                        ->label('Full Name')
                        ->required(),
                    InputField::make('email')
                        ->label('Email')
                        ->email()
                        ->required(),
                ]),
            Section::make('preferences')
                ->label('Preferences')
                ->description('Configure your preferences')
                ->fields([CheckboxField::make('newsletter')->label('Subscribe to newsletter')]),
        ]);

        $renderedForm = $form->render();

        $this->assertStringContainsString('Personal Information', $renderedForm);
        $this->assertStringContainsString('Basic personal details', $renderedForm);
        $this->assertStringContainsString('Preferences', $renderedForm);
        $this->assertStringContainsString('Configure your preferences', $renderedForm);
        $this->assertStringContainsString('form-section', $renderedForm);
        $this->assertStringContainsString('section-header', $renderedForm);
    }

    #[Test]
    public function it_can_render_form_with_different_column_layouts()
    {
        $form = new Form;

        $form->columns(3);

        $form->schema([
            InputField::make('name')->label('Name'),
            InputField::make('email')->label('Email'),
            InputField::make('phone')->label('Phone'),
        ]);

        $renderedForm = $form->render();

        $this->assertStringContainsString('grid-cols-3', $renderedForm);
    }

    #[Test]
    public function it_can_render_form_with_column_spanning()
    {
        $form = new Form;

        $form->schema([
            InputField::make('name')
                ->label('Name')
                ->columnSpan('2'),
            InputField::make('email')->label('Email'),
        ]);

        $renderedForm = $form->render();

        $this->assertStringContainsString('col-span-2', $renderedForm);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    #[Test]
    public function it_can_render_input_field_with_all_attributes()
    {
        $field = InputField::make('name')
            ->label('Full Name')
            ->placeholder('Enter your full name')
            ->required()
            ->maxLength(255)
            ->minLength(2)
            ->helperText('This will be your display name')
            ->attributes(['data-test' => 'name-field']);

        $rendered = $field->render();

        $this->assertStringContainsString('Full Name', $rendered);
        $this->assertStringContainsString('Enter your full name', $rendered);
        $this->assertStringContainsString('required', $rendered);
        $this->assertStringContainsString('maxlength="255"', $rendered);
        $this->assertStringContainsString('minlength="2"', $rendered);
        $this->assertStringContainsString('This will be your display name', $rendered);
        $this->assertStringContainsString('data-test="name-field"', $rendered);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_can_render_select_field_with_options()
    {
        $field = SelectField::make('country')
            ->label('Country')
            ->options([
                'us' => 'United States',
                'uk' => 'United Kingdom',
                'ca' => 'Canada',
            ])
            ->emptyOption('Select a country')
            ->required();

        $rendered = $field->render();

        $this->assertStringContainsString('Country', $rendered);
        $this->assertStringContainsString('Select a country', $rendered);
        $this->assertStringContainsString('<option value="">Select a country</option>', $rendered);
        $this->assertMatchesRegularExpression('/<option value="us">\s*United States\s*<\/option>/', $rendered);
        $this->assertMatchesRegularExpression('/<option value="uk">\s*United Kingdom\s*<\/option>/', $rendered);
        $this->assertMatchesRegularExpression('/<option value="ca">\s*Canada\s*<\/option>/', $rendered);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_can_render_multiple_select_field()
    {
        $field = SelectField::make('skills')
            ->label('Skills')
            ->multiple()
            ->options([
                'php' => 'PHP',
                'js' => 'JavaScript',
                'python' => 'Python',
            ]);

        $rendered = $field->render();

        $this->assertStringContainsString('wire:model.defer="formData.skills"', $rendered);
        $this->assertStringContainsString('multiple', $rendered);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    #[Test]
    public function it_can_render_checkbox_field()
    {
        $field = CheckboxField::make('terms')
            ->label('I agree to the terms and conditions')
            ->required();

        $rendered = $field->render();

        $this->assertStringContainsString('I agree to the terms and conditions', $rendered);
        $this->assertStringContainsString('type="checkbox"', $rendered);
        $this->assertStringContainsString('wire:model.defer="formData.terms"', $rendered);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_renders_helper_text()
    {
        $field = InputField::make('password')
            ->label('Password')
            ->helperText('Password must be at least 8 characters long');

        $rendered = $field->render();

        $this->assertStringContainsString('Password must be at least 8 characters long', $rendered);
        $this->assertStringContainsString('form-help', $rendered);
    }
}
