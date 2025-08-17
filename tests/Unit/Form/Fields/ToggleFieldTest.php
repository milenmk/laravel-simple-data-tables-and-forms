<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form\Fields;

use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\ToggleField;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class ToggleFieldTest extends BaseTest
{
    /**
     * @test
     */
    public function toggle_field_creation(): void
    {
        $field = new ToggleField('active');

        $this->assertEquals('active', $field->name);
        $this->assertEquals(1, $field->onValue);
        $this->assertEquals(0, $field->offValue);
        $this->assertNull($field->onLabel);
        $this->assertNull($field->offLabel);
        $this->assertEquals('md', $field->size);
        $this->assertEquals('primary', $field->color);
    }

    /**
     * @test
     */
    public function toggle_field_on_value_configuration(): void
    {
        $field = new ToggleField('active');
        $result = $field->onValue('enabled');

        $this->assertEquals('enabled', $field->onValue);
        $this->assertSame($field, $result); // Test fluent interface
    }

    /**
     * @test
     */
    public function toggle_field_off_value_configuration(): void
    {
        $field = new ToggleField('active');
        $result = $field->offValue('disabled');

        $this->assertEquals('disabled', $field->offValue);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function toggle_field_on_label_configuration(): void
    {
        $field = new ToggleField('active');
        $result = $field->onLabel('On');

        $this->assertEquals('On', $field->onLabel);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function toggle_field_off_label_configuration(): void
    {
        $field = new ToggleField('active');
        $result = $field->offLabel('Off');

        $this->assertEquals('Off', $field->offLabel);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function toggle_field_color_configuration(): void
    {
        $field = new ToggleField('active');
        $result = $field->color('success');

        $this->assertEquals('success', $field->color);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function toggle_field_size_configuration(): void
    {
        $field = new ToggleField('active');
        $result = $field->size('lg');

        $this->assertEquals('lg', $field->size);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function toggle_field_small_helper(): void
    {
        $field = new ToggleField('active');
        $result = $field->small();

        $this->assertEquals('sm', $field->size);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function toggle_field_large_helper(): void
    {
        $field = new ToggleField('active');
        $result = $field->large();

        $this->assertEquals('lg', $field->size);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function toggle_field_with_boolean_values(): void
    {
        $field = new ToggleField('terms_accepted');
        $field->onValue(true)->offValue(false);

        $this->assertTrue($field->onValue);
        $this->assertFalse($field->offValue);
    }

    /**
     * @test
     */
    public function toggle_field_fluent_interface(): void
    {
        $field = new ToggleField('notifications');

        $result = $field
            ->onValue('yes')
            ->offValue('no')
            ->onLabel('Enable')
            ->offLabel('Disable')
            ->color('success')
            ->large();

        $this->assertEquals('yes', $field->onValue);
        $this->assertEquals('no', $field->offValue);
        $this->assertEquals('Enable', $field->onLabel);
        $this->assertEquals('Disable', $field->offLabel);
        $this->assertEquals('success', $field->color);
        $this->assertEquals('lg', $field->size);
        $this->assertSame($field, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     *
     * @test
     */
    public function toggle_field_render(): void
    {
        $field = new ToggleField('active');
        $field
            ->onValue('yes')
            ->offValue('no')
            ->onLabel('Active')
            ->offLabel('Inactive');

        $rendered = $field->render();

        $this->assertStringContainsString('name="active"', $rendered);
    }
}
