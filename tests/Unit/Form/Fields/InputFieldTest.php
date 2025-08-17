<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form\Fields;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use Throwable;

class InputFieldTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock the $errors variable for view rendering
        View::share('errors', new ViewErrorBag);
    }

    #[Test]
    public function input_field_can_be_created()
    {
        $field = InputField::make('name');

        $this->assertEquals('name', $field->name);
    }

    #[Test]
    public function input_field_can_set_label()
    {
        $field = InputField::make('name')->label('Full Name');

        $this->assertEquals('Full Name', $field->getLabel());
    }

    #[Test]
    public function input_field_can_set_placeholder()
    {
        $field = InputField::make('name')->placeholder('Enter your name');

        $this->assertEquals('Enter your name', $field->placeholder);
    }

    #[Test]
    public function input_field_can_be_required()
    {
        $field = InputField::make('name')->required();

        $this->assertTrue($field->required);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function input_field_can_be_disabled()
    {
        $field = InputField::make('name')->disabled();

        $this->assertTrue($field->isDisabled());
    }

    #[Test]
    public function input_field_can_set_default_value()
    {
        $field = InputField::make('name')->default('John Doe');

        $this->assertEquals('John Doe', $field->default);
    }

    #[Test]
    public function input_field_can_set_max_length()
    {
        $field = InputField::make('name')->maxLength(255);

        $this->assertEquals(255, $field->maxLength);
    }

    #[Test]
    public function input_field_can_set_min_length()
    {
        $field = InputField::make('name')->minLength(3);

        $this->assertEquals(3, $field->minLength);
    }

    #[Test]
    public function input_field_can_set_email_type()
    {
        $field = InputField::make('email')->email();

        $this->assertEquals('email', $field->type);
    }

    #[Test]
    public function input_field_can_set_password_type()
    {
        $field = InputField::make('password')->password();

        $this->assertEquals('password', $field->type);
    }

    #[Test]
    public function input_field_can_set_number_type()
    {
        $field = InputField::make('age')->number();

        $this->assertEquals('number', $field->type);
    }

    #[Test]
    public function input_field_can_set_url_type()
    {
        $field = InputField::make('website')->url();

        $this->assertEquals('url', $field->type);
    }

    #[Test]
    public function input_field_can_set_tel_type()
    {
        $field = InputField::make('phone')->tel();

        $this->assertEquals('tel', $field->type);
    }

    #[Test]
    public function input_field_can_set_date_type()
    {
        $field = InputField::make('birth_date')->date();

        $this->assertEquals('date', $field->type);
    }

    #[Test]
    public function input_field_can_set_time_type()
    {
        $field = InputField::make('start_time')->time();

        $this->assertEquals('time', $field->type);
    }

    #[Test]
    public function input_field_can_set_datetime_type()
    {
        $field = InputField::make('appointment')->datetime();

        $this->assertEquals('datetime-local', $field->type);
    }

    #[Test]
    public function input_field_can_set_pattern()
    {
        $field = InputField::make('code')->pattern('[A-Z0-9]+');

        $this->assertEquals('[A-Z0-9]+', $field->pattern);
    }

    #[Test]
    public function input_field_can_set_min_max_for_numbers()
    {
        $field = InputField::make('age')
            ->number()
            ->min(0)
            ->max(120);

        $this->assertEquals(0, $field->min);
        $this->assertEquals(120, $field->max);
    }

    #[Test]
    public function input_field_can_set_step()
    {
        $field = InputField::make('price')
            ->number()
            ->step(1);

        $this->assertEquals(1, $field->step);
    }

    #[Test]
    public function input_field_can_set_helper_text()
    {
        $field = InputField::make('username')->helperText('Must be unique');

        $this->assertEquals('Must be unique', $field->helperText);
    }

    #[Test]
    public function input_field_can_set_column_span()
    {
        $field = InputField::make('address')->columnSpan('2');

        $this->assertEquals('2', $field->columnSpan);
    }

    #[Test]
    public function input_field_can_set_attributes()
    {
        $attributes = ['data-test' => 'value', 'class' => 'custom-class'];
        $field = InputField::make('name')->attributes($attributes);

        $this->assertEquals($attributes, $field->attributes);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function input_field_renders_as_string()
    {
        $field = InputField::make('name')->label('Full Name');

        // Provide a dummy error bag
        session()->flash('errors', new ViewErrorBag);

        $rendered = $field->render();

        $this->assertIsString($rendered);
        $this->assertStringContainsString('Full Name', $rendered);
        $this->assertStringContainsString('name="name"', $rendered);
    }

    #[Test]
    public function input_field_gets_validation_rules()
    {
        $field = InputField::make('email')
            ->email()
            ->maxLength(255);

        $rules = $field->getValidationRules();

        // The required rule is handled at the Form level, not by InputField
        $this->assertContains('email', $rules);
        $this->assertContains('max:255', $rules);
    }
}
