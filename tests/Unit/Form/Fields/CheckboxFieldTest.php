<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form\Fields;

use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\CheckboxField;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class CheckboxFieldTest extends BaseTest
{
    #[Test]
    public function checkbox_field_creation(): void
    {
        $field = new CheckboxField('active');

        $this->assertEquals('active', $field->name);
        $this->assertEquals(1, $field->checkedValue);
        $this->assertEquals(0, $field->uncheckedValue);
        $this->assertFalse($field->inline);
    }

    #[Test]
    public function checkbox_field_checked_value_configuration(): void
    {
        $field = new CheckboxField('active');
        $result = $field->checkedValue('yes');

        $this->assertEquals('yes', $field->checkedValue);
        $this->assertSame($field, $result); // Test fluent interface
    }

    #[Test]
    public function checkbox_field_unchecked_value_configuration(): void
    {
        $field = new CheckboxField('active');
        $result = $field->uncheckedValue('no');

        $this->assertEquals('no', $field->uncheckedValue);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function checkbox_field_inline_configuration(): void
    {
        $field = new CheckboxField('active');

        // Test enabling inline
        $result = $field->inline();
        $this->assertTrue($field->inline);
        $this->assertSame($field, $result);

        // Test disabling inline
        $field->inline(false);
        $this->assertFalse($field->inline);

        // Test default parameter (true)
        $field->inline();
        $this->assertTrue($field->inline);
    }

    #[Test]
    public function checkbox_field_with_boolean_values(): void
    {
        $field = new CheckboxField('terms_accepted');
        $field->checkedValue(true)->uncheckedValue(false);

        $this->assertTrue($field->checkedValue);
        $this->assertFalse($field->uncheckedValue);
    }

    #[Test]
    public function checkbox_field_with_string_values(): void
    {
        $field = new CheckboxField('newsletter');
        $field->checkedValue('subscribe')->uncheckedValue('unsubscribe');

        $this->assertEquals('subscribe', $field->checkedValue);
        $this->assertEquals('unsubscribe', $field->uncheckedValue);
    }

    #[Test]
    public function checkbox_field_fluent_interface(): void
    {
        $field = new CheckboxField('active');

        $result = $field
            ->checkedValue('enabled')
            ->uncheckedValue('disabled')
            ->inline();

        $this->assertEquals('enabled', $field->checkedValue);
        $this->assertEquals('disabled', $field->uncheckedValue);
        $this->assertTrue($field->inline);
        $this->assertSame($field, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function checkbox_field_render(): void
    {
        $field = new CheckboxField('active');
        $field->checkedValue('yes')->uncheckedValue('no');

        $rendered = $field->render();

        $this->assertStringContainsString('name="active"', $rendered);
        $this->assertStringContainsString('type="checkbox"', $rendered);
    }
}
