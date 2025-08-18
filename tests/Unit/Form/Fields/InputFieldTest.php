<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form\Fields;

use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;

class InputFieldTest extends BaseTest
{
    #[Test]
    public function it_can_be_created_with_name()
    {
        $field = InputField::make('test_field');

        $this->assertEquals('test_field', $field->name);
        $this->assertEquals('text', $field->type);
        $this->assertNull($field->minLength);
        $this->assertNull($field->maxLength);
        $this->assertNull($field->pattern);
        $this->assertNull($field->min);
        $this->assertNull($field->max);
        $this->assertNull($field->step);
    }

    #[Test]
    public function it_can_set_email_type()
    {
        $field = InputField::make('email');
        $result = $field->email();

        $this->assertEquals('email', $field->type);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_custom_type()
    {
        $field = InputField::make('test');
        $result = $field->type('search');

        $this->assertEquals('search', $field->type);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_password_type()
    {
        $field = InputField::make('password');
        $result = $field->password();

        $this->assertEquals('password', $field->type);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_number_type()
    {
        $field = InputField::make('age');
        $result = $field->number();

        $this->assertEquals('number', $field->type);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_tel_type()
    {
        $field = InputField::make('phone');
        $result = $field->tel();

        $this->assertEquals('tel', $field->type);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_url_type()
    {
        $field = InputField::make('website');
        $result = $field->url();

        $this->assertEquals('url', $field->type);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_min_length()
    {
        $field = InputField::make('username');
        $result = $field->minLength(3);

        $this->assertEquals(3, $field->minLength);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_max_length()
    {
        $field = InputField::make('username');
        $result = $field->maxLength(50);

        $this->assertEquals(50, $field->maxLength);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_pattern()
    {
        $field = InputField::make('code');
        $result = $field->pattern('[A-Z]{3}[0-9]{3}');

        $this->assertEquals('[A-Z]{3}[0-9]{3}', $field->pattern);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_min_value()
    {
        $field = InputField::make('age');
        $result = $field->min(18);

        $this->assertEquals(18, $field->min);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_max_value()
    {
        $field = InputField::make('age');
        $result = $field->max(100);

        $this->assertEquals(100, $field->max);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_step()
    {
        $field = InputField::make('price');
        $result = $field->step(0.01);

        $this->assertEquals(0.01, $field->step);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_chain_multiple_configurations()
    {
        $field = InputField::make('price')
            ->number()
            ->min(0)
            ->max(1000)
            ->step(0.01);

        $this->assertEquals('number', $field->type);
        $this->assertEquals(0, $field->min);
        $this->assertEquals(1000, $field->max);
        $this->assertEquals(0.01, $field->step);
    }

    #[Test]
    public function it_can_set_length_constraints()
    {
        $field = InputField::make('username')
            ->minLength(3)
            ->maxLength(20)
            ->pattern('[a-zA-Z0-9_]+');

        $this->assertEquals(3, $field->minLength);
        $this->assertEquals(20, $field->maxLength);
        $this->assertEquals('[a-zA-Z0-9_]+', $field->pattern);
    }

    #[Test]
    public function it_inherits_from_field_class()
    {
        $field = InputField::make('test')
            ->label('Test Field')
            ->required()
            ->placeholder('Enter test value');

        $this->assertEquals('Test Field', $field->label);
        $this->assertTrue($field->required);
        $this->assertEquals('Enter test value', $field->placeholder);
    }

    #[Test]
    public function it_can_set_search_type()
    {
        $field = InputField::make('search');
        $result = $field->search();

        $this->assertEquals('search', $field->type);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_hidden_type()
    {
        $field = InputField::make('hidden');
        $result = $field->type('hidden');

        $this->assertEquals('hidden', $field->type);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_date_type()
    {
        $field = InputField::make('birthdate');
        $result = $field->date();

        $this->assertEquals('date', $field->type);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_time_type()
    {
        $field = InputField::make('appointment_time');
        $result = $field->time();

        $this->assertEquals('time', $field->type);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_datetime_local_type()
    {
        $field = InputField::make('appointment');
        $result = $field->type('datetime-local');

        $this->assertEquals('datetime-local', $field->type);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_color_type()
    {
        $field = InputField::make('favorite_color');
        $result = $field->type('color');

        $this->assertEquals('color', $field->type);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_range_type()
    {
        $field = InputField::make('volume');
        $result = $field->type('range');

        $this->assertEquals('range', $field->type);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_file_type()
    {
        $field = InputField::make('upload');
        $result = $field->file();

        $this->assertEquals('file', $field->type);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_numeric_constraints_with_decimals()
    {
        $field = InputField::make('price')
            ->number()
            ->min(0.01)
            ->max(999.99)
            ->step(0.01);

        $this->assertEquals('number', $field->type);
        $this->assertEquals(0.01, $field->min);
        $this->assertEquals(999.99, $field->max);
        $this->assertEquals(0.01, $field->step);
    }

    #[Test]
    public function it_can_set_negative_min_value()
    {
        $field = InputField::make('temperature');
        $result = $field->min(-50);

        $this->assertEquals(-50, $field->min);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_zero_values()
    {
        $field = InputField::make('count')
            ->min(0)
            ->max(0)
            ->step(0);

        $this->assertEquals(0, $field->min);
        $this->assertEquals(0, $field->max);
        $this->assertEquals(0, $field->step);
    }

    #[Test]
    public function it_can_set_complex_pattern()
    {
        $field = InputField::make('phone');
        $pattern = '^\+?[1-9]\d{1,14}$';
        $result = $field->pattern($pattern);

        $this->assertEquals($pattern, $field->pattern);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function it_can_set_large_length_values()
    {
        $field = InputField::make('description')
            ->minLength(100)
            ->maxLength(5000);

        $this->assertEquals(100, $field->minLength);
        $this->assertEquals(5000, $field->maxLength);
    }
}
