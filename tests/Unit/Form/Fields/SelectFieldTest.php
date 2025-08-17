<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form\Fields;

use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\SelectField;
use PHPUnit\Framework\TestCase;

class SelectFieldTest extends TestCase
{
    /**
     * @test
     */
    public function get_current_value_returns_null_for_empty_single_select()
    {
        $field = SelectField::make('test_field')->options(['1' => 'Option 1', '2' => 'Option 2']);

        $field->setFormContext(null, []);

        $this->assertNull($field->getCurrentValue());
    }

    /**
     * @test
     */
    public function get_current_value_returns_empty_array_for_empty_multiple_select()
    {
        $field = SelectField::make('test_field')
            ->options(['1' => 'Option 1', '2' => 'Option 2'])
            ->multiple();

        $field->setFormContext(null, []);

        $this->assertEquals([], $field->getCurrentValue());
    }

    /**
     * @test
     */
    public function get_current_value_returns_correct_value_for_single_select()
    {
        $field = SelectField::make('test_field')->options(['1' => 'Option 1', '2' => 'Option 2']);

        $field->setFormContext(null, ['test_field' => '2']);

        $this->assertEquals('2', $field->getCurrentValue());
    }

    /**
     * @test
     */
    public function get_current_value_returns_correct_array_for_multiple_select()
    {
        $field = SelectField::make('test_field')
            ->options(['1' => 'Option 1', '2' => 'Option 2', '3' => 'Option 3'])
            ->multiple();

        $field->setFormContext(null, ['test_field' => ['1', '3']]);

        $this->assertEquals(['1', '3'], $field->getCurrentValue());
    }

    /**
     * @test
     */
    public function get_current_value_converts_single_value_to_array_for_multiple_select()
    {
        $field = SelectField::make('test_field')
            ->options(['1' => 'Option 1', '2' => 'Option 2'])
            ->multiple();

        $field->setFormContext(null, ['test_field' => '1']);

        $this->assertEquals(['1'], $field->getCurrentValue());
    }

    /**
     * @test
     */
    public function get_current_value_handles_null_value_for_single_select()
    {
        $field = SelectField::make('test_field')->options(['1' => 'Option 1', '2' => 'Option 2']);

        $field->setFormContext(null, ['test_field' => null]);

        $this->assertNull($field->getCurrentValue());
    }

    /**
     * @test
     */
    public function get_current_value_handles_empty_string_for_single_select()
    {
        $field = SelectField::make('test_field')->options(['1' => 'Option 1', '2' => 'Option 2']);

        $field->setFormContext(null, ['test_field' => '']);

        $this->assertNull($field->getCurrentValue());
    }

    /**
     * @test
     */
    public function get_current_value_handles_null_value_for_multiple_select()
    {
        $field = SelectField::make('test_field')
            ->options(['1' => 'Option 1', '2' => 'Option 2'])
            ->multiple();

        $field->setFormContext(null, ['test_field' => null]);

        $this->assertEquals([], $field->getCurrentValue());
    }

    /**
     * @test
     */
    public function get_current_value_handles_empty_string_for_multiple_select()
    {
        $field = SelectField::make('test_field')
            ->options(['1' => 'Option 1', '2' => 'Option 2'])
            ->multiple();

        $field->setFormContext(null, ['test_field' => '']);

        $this->assertEquals([], $field->getCurrentValue());
    }

    /**
     * @test
     */
    public function get_current_value_uses_default_value_when_no_form_data()
    {
        $field = SelectField::make('test_field')
            ->options(['1' => 'Option 1', '2' => 'Option 2'])
            ->default('1');

        $field->setFormContext(null, []);

        $this->assertEquals('1', $field->getCurrentValue());
    }

    /**
     * @test
     */
    public function get_current_value_uses_default_array_for_multiple_select()
    {
        $field = SelectField::make('test_field')
            ->options(['1' => 'Option 1', '2' => 'Option 2'])
            ->multiple()
            ->default(['1']);

        $field->setFormContext(null, []);

        $this->assertEquals(['1'], $field->getCurrentValue());
    }
}
