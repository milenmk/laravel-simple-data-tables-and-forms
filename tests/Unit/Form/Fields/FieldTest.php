<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form\Fields;

use Closure;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\Field;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use ReflectionException;

class FieldTest extends BaseTest
{
    private Field $field;

    protected function setUp(): void
    {
        parent::setUp();

        // Use InputField as a concrete implementation of Field for testing
        $this->field = InputField::make('test_field');
    }

    #[Test]
    public function it_can_be_created_with_name()
    {
        $field = InputField::make('username');

        $this->assertEquals('username', $field->name);
    }

    #[Test]
    public function it_can_set_and_get_label()
    {
        $this->field->label('Test Label');

        $this->assertEquals('Test Label', $this->field->label);
        $this->assertEquals('Test Label', $this->field->getLabel());
    }

    #[Test]
    public function it_generates_label_from_name_when_not_set()
    {
        $field = InputField::make('first_name');

        $this->assertEquals('First Name', $field->getLabel());
    }

    #[Test]
    public function it_can_set_default_value()
    {
        $this->field->default('default_value');

        $this->assertEquals('default_value', $this->field->default);
    }

    #[Test]
    public function it_can_set_required()
    {
        $this->field->required();

        $this->assertTrue($this->field->required);

        $this->field->required(false);

        $this->assertFalse($this->field->required);
    }

    #[Test]
    public function it_can_set_placeholder_as_string()
    {
        $this->field->placeholder('Enter value');

        $this->assertEquals('Enter value', $this->field->placeholder);
    }

    #[Test]
    public function it_can_set_placeholder_as_closure()
    {
        $closure = fn () => 'Dynamic placeholder';
        $this->field->placeholder($closure);

        $this->assertInstanceOf(Closure::class, $this->field->placeholder);
    }

    #[Test]
    public function it_can_set_helper_text()
    {
        $this->field->helperText('This is helper text');

        $this->assertEquals('This is helper text', $this->field->helperText);
    }

    #[Test]
    public function it_can_set_validation_rules()
    {
        $rules = ['required', 'string', 'max:255'];
        $this->field->rules($rules);

        $this->assertEquals($rules, $this->field->rules);
    }

    #[Test]
    public function it_validates_and_sanitizes_rules()
    {
        // Test with allowed rules
        $allowedRules = ['required', 'string', 'email', 'max:255'];
        $this->field->rules($allowedRules);

        $this->assertEquals($allowedRules, $this->field->rules);

        // Test with disallowed rules (should be filtered out)
        $mixedRules = ['required', 'string', 'dangerous_rule', 'max:255'];
        $this->field->rules($mixedRules);

        // The dangerous_rule should be filtered out
        $this->assertNotContains('dangerous_rule', $this->field->rules);
        $this->assertContains('required', $this->field->rules);
        $this->assertContains('string', $this->field->rules);
        $this->assertContains('max:255', $this->field->rules);
    }

    #[Test]
    public function it_can_set_validation_messages()
    {
        $messages = ['required' => 'This field is required'];
        $this->field->validationMessages($messages);

        $this->assertEquals($messages, $this->field->validationMessages);
    }

    #[Test]
    public function it_can_set_column_span()
    {
        $this->field->columnSpan('2');

        $this->assertEquals('2', $this->field->columnSpan);
    }

    #[Test]
    public function it_can_set_prefix_icon()
    {
        $this->field->prefixIcon('user');

        $this->assertEquals('user', $this->field->prefixIcon);
    }

    #[Test]
    public function it_can_set_suffix_icon()
    {
        $this->field->suffixIcon('search');

        $this->assertEquals('search', $this->field->suffixIcon);
    }

    #[Test]
    public function it_can_set_extra_attributes_as_array()
    {
        $attributes = ['data-test' => 'value', 'class' => 'custom-class'];
        $this->field->extraAttributes($attributes);

        $this->assertEquals($attributes, $this->field->extraAttributes);
    }

    #[Test]
    public function it_can_set_extra_attributes_as_closure()
    {
        $closure = fn () => ['data-dynamic' => 'value'];
        $this->field->extraAttributes($closure);

        $this->assertInstanceOf(Closure::class, $this->field->extraAttributesCallback);
    }

    #[Test]
    public function it_can_set_after_state_updated_callback()
    {
        $callback = fn ($state) => null;
        $this->field->afterStateUpdated($callback);

        $this->assertInstanceOf(Closure::class, $this->field->afterStateUpdated);
    }

    #[Test]
    public function it_can_set_hidden_as_boolean()
    {
        $this->field->hidden();

        $this->assertInstanceOf(Closure::class, $this->field->hidden);
    }

    #[Test]
    public function it_can_set_hidden_as_closure()
    {
        $closure = fn () => true;
        $this->field->hidden($closure);

        $this->assertInstanceOf(Closure::class, $this->field->hidden);
    }

    #[Test]
    public function it_can_set_disabled_as_boolean()
    {
        $this->field->disabled();

        $this->assertInstanceOf(Closure::class, $this->field->disabled);
    }

    #[Test]
    public function it_can_set_disabled_as_closure()
    {
        $closure = fn () => true;
        $this->field->disabled($closure);

        $this->assertInstanceOf(Closure::class, $this->field->disabled);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function it_can_set_form_context()
    {
        $model = new TestModel;
        $formData = ['field1' => 'value1'];

        $this->field->setFormContext($model, $formData);

        // Use reflection to access protected properties
        $reflection = new ReflectionClass($this->field);
        $recordProperty = $reflection->getProperty('record');

        $formDataProperty = $reflection->getProperty('formData');

        $this->assertSame($model, $recordProperty->getValue($this->field));
        $this->assertEquals($formData, $formDataProperty->getValue($this->field));
    }

    #[Test]
    public function it_can_set_attributes()
    {
        $attributes = ['class' => 'form-control', 'data-test' => 'value'];
        $this->field->attributes($attributes);

        $this->assertEquals($attributes, $this->field->attributes);
    }

    #[Test]
    public function it_sanitizes_attributes_for_security()
    {
        $attributes = [
            'class' => 'form-control',
            'onclick' => 'alert("xss")', // Should be filtered out
            'data-safe' => 'value',
            'aria-label' => 'Safe label',
        ];

        $this->field->attributes($attributes);

        $this->assertArrayHasKey('class', $this->field->attributes);
        $this->assertArrayHasKey('data-safe', $this->field->attributes);
        $this->assertArrayHasKey('aria-label', $this->field->attributes);
        $this->assertArrayNotHasKey('onclick', $this->field->attributes);
    }

    #[Test]
    public function it_can_get_attribute()
    {
        $this->field->attributes(['class' => 'form-control']);

        $this->assertEquals('form-control', $this->field->getAttribute('class'));
        $this->assertEquals('default', $this->field->getAttribute('nonexistent', 'default'));
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function it_can_check_if_hidden_with_closure()
    {
        $this->field->hidden(fn () => true);
        $this->field->setFormContext(null, []);

        $this->assertTrue($this->field->isHidden());
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function it_returns_false_for_hidden_when_no_closure_set()
    {
        $this->assertFalse($this->field->isHidden());
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function it_can_check_if_disabled_with_closure()
    {
        $this->field->disabled(fn () => true);
        $this->field->setFormContext(null, []);

        $this->assertTrue($this->field->isDisabled());
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function it_can_check_if_disabled_with_boolean()
    {
        $this->field->disabled();

        $this->assertTrue($this->field->isDisabled());

        $this->field->disabled(false);

        $this->assertFalse($this->field->isDisabled());
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function it_can_execute_after_state_updated_callback()
    {
        $executed = false;
        $this->field->afterStateUpdated(function () use (&$executed) {
            $executed = true;
        });
        $this->field->setFormContext(null, []);

        $this->field->executeAfterStateUpdated('test_value');

        $this->assertTrue($executed);
    }

    #[Test]
    public function it_can_get_merged_attributes()
    {
        $this->field->extraAttributes(['data-extra' => 'value']);
        $baseAttributes = ['class' => 'base-class'];

        $merged = $this->field->getMergedAttributes($baseAttributes);

        $this->assertArrayHasKey('class', $merged);
        $this->assertArrayHasKey('data-extra', $merged);
    }

    #[Test]
    public function it_merges_class_attributes_correctly()
    {
        $this->field->extraAttributes(['class' => 'extra-class']);
        $baseAttributes = ['class' => 'base-class'];

        $merged = $this->field->getMergedAttributes($baseAttributes);

        $this->assertEquals('base-class extra-class', $merged['class']);
    }

    #[Test]
    public function it_can_get_extra_attributes()
    {
        $attributes = ['data-test' => 'value'];
        $this->field->extraAttributes($attributes);

        $this->assertEquals($attributes, $this->field->getExtraAttributes());
    }

    #[Test]
    public function it_can_get_extra_attributes_from_callback()
    {
        $this->field->extraAttributes(fn () => ['data-dynamic' => 'value']);
        $this->field->setFormContext(null, []);

        $attributes = $this->field->getExtraAttributes();

        $this->assertArrayHasKey('data-dynamic', $attributes);
        $this->assertEquals('value', $attributes['data-dynamic']);
    }

    #[Test]
    public function it_can_get_current_value_from_form_data()
    {
        $this->field->setFormContext(null, ['test_field' => 'form_value']);

        $this->assertEquals('form_value', $this->field->getCurrentValue());
    }

    #[Test]
    public function it_returns_default_when_no_form_data()
    {
        $this->field->default('default_value');
        $this->field->setFormContext(null, []);

        $this->assertEquals('default_value', $this->field->getCurrentValue());
    }

    #[Test]
    public function it_can_get_validation_rules()
    {
        $this->field->required()->rules(['string', 'max:255']);

        $rules = $this->field->getValidationRules();

        $this->assertContains('required', $rules);
        $this->assertContains('string', $rules);
        $this->assertContains('max:255', $rules);
    }

    #[Test]
    public function it_can_set_reactive()
    {
        $this->field->reactive();

        $this->assertTrue($this->field->reactive);

        $this->field->reactive(false);

        $this->assertFalse($this->field->reactive);
    }

    #[Test]
    public function it_sanitizes_rule_parameters_for_unique_and_exists()
    {
        $rules = ['unique:users,email', 'exists:categories,id'];
        $this->field->rules($rules);

        $this->assertContains('unique:users,email', $this->field->rules);
        $this->assertContains('exists:categories,id', $this->field->rules);
    }

    #[Test]
    public function it_sanitizes_rule_parameters_for_in_and_not_in()
    {
        $rules = ['in:active,inactive', 'not_in:banned,suspended'];
        $this->field->rules($rules);

        $this->assertContains('in:active,inactive', $this->field->rules);
        $this->assertContains('not_in:banned,suspended', $this->field->rules);
    }

    #[Test]
    public function it_validates_regex_rule_parameters()
    {
        // Valid regex
        $validRules = ['regex:/^[A-Z]+$/'];
        $this->field->rules($validRules);

        $this->assertContains('regex:/^[A-Z]+$/', $this->field->rules);
    }

    #[Test]
    public function it_handles_invalid_regex_rule_parameters()
    {
        // Invalid regex - should fallback to string
        $invalidRules = ['regex:/[unclosed'];
        $this->field->rules($invalidRules);

        // Should fallback to string validation
        $this->assertContains('string', $this->field->rules);
        $this->assertNotContains('regex:/[unclosed', $this->field->rules);
    }

    #[Test]
    public function it_sanitizes_numeric_rule_parameters()
    {
        $rules = ['min:5', 'max:100', 'between:1,10'];
        $this->field->rules($rules);

        $this->assertContains('min:5', $this->field->rules);
        $this->assertContains('max:100', $this->field->rules);
        $this->assertContains('between:1,10', $this->field->rules);
    }
}
