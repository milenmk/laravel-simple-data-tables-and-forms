<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form\Fields;

use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\DateTimeField;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class DateTimeFieldTest extends BaseTest
{
    /**
     * @test
     */
    public function datetime_field_creation(): void
    {
        $field = new DateTimeField('created_at');

        $this->assertEquals('created_at', $field->name);
        $this->assertEquals('datetime-local', $field->type);
        $this->assertEquals('Y-m-d\TH:i', $field->format);
        $this->assertNull($field->min);
        $this->assertNull($field->max);
        $this->assertNull($field->step);
        $this->assertFalse($field->withSeconds);
    }

    /**
     * @test
     */
    public function datetime_field_date_type(): void
    {
        $field = new DateTimeField('birth_date');
        $result = $field->date();

        $this->assertEquals('date', $field->type);
        $this->assertEquals('Y-m-d', $field->format);
        $this->assertSame($field, $result); // Test fluent interface
    }

    /**
     * @test
     */
    public function datetime_field_time_type(): void
    {
        $field = new DateTimeField('start_time');
        $result = $field->time();

        $this->assertEquals('time', $field->type);
        $this->assertEquals('H:i', $field->format);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function datetime_field_datetime_type(): void
    {
        $field = new DateTimeField('appointment');
        $result = $field->datetime();

        $this->assertEquals('datetime-local', $field->type);
        $this->assertEquals('Y-m-d\TH:i', $field->format);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function datetime_field_with_seconds_for_time(): void
    {
        $field = new DateTimeField('precise_time');
        $field->time();
        $result = $field->withSeconds();

        $this->assertTrue($field->withSeconds);
        $this->assertEquals('H:i:s', $field->format);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function datetime_field_with_seconds_for_datetime(): void
    {
        $field = new DateTimeField('precise_datetime');
        $field->datetime();
        $result = $field->withSeconds();

        $this->assertTrue($field->withSeconds);
        $this->assertEquals('Y-m-d\TH:i:s', $field->format);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function datetime_field_with_seconds_disabled(): void
    {
        $field = new DateTimeField('appointment');
        $field->withSeconds();
        $field->withSeconds(false);

        $this->assertFalse($field->withSeconds);
    }

    /**
     * @test
     */
    public function datetime_field_min_configuration(): void
    {
        $field = new DateTimeField('appointment');
        $result = $field->min('2024-01-01');

        $this->assertEquals('2024-01-01', $field->min);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function datetime_field_max_configuration(): void
    {
        $field = new DateTimeField('appointment');
        $result = $field->max('2024-12-31');

        $this->assertEquals('2024-12-31', $field->max);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function datetime_field_step_configuration(): void
    {
        $field = new DateTimeField('appointment');
        $result = $field->step(60);

        $this->assertEquals(60, $field->step);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function datetime_field_format_configuration(): void
    {
        $field = new DateTimeField('appointment');
        $result = $field->format('d/m/Y H:i');

        $this->assertEquals('d/m/Y H:i', $field->format);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function datetime_field_validation_rules_for_date(): void
    {
        $field = new DateTimeField('birth_date');
        $field
            ->date()
            ->min('1900-01-01')
            ->max('2024-12-31');

        $rules = $field->getValidationRules();

        $this->assertContains('date', $rules);
        $this->assertContains('after_or_equal:1900-01-01', $rules);
        $this->assertContains('before_or_equal:2024-12-31', $rules);
    }

    /**
     * @test
     */
    public function datetime_field_validation_rules_for_time(): void
    {
        $field = new DateTimeField('start_time');
        $field->time();

        $rules = $field->getValidationRules();

        $this->assertContains('date_format:H:i', $rules);
    }

    /**
     * @test
     */
    public function datetime_field_validation_rules_for_time_with_seconds(): void
    {
        $field = new DateTimeField('precise_time');
        $field->time()->withSeconds();

        $rules = $field->getValidationRules();

        $this->assertContains('date_format:H:i:s', $rules);
    }

    /**
     * @test
     */
    public function datetime_field_validation_rules_for_datetime(): void
    {
        $field = new DateTimeField('appointment');
        $field
            ->datetime()
            ->min('2024-01-01T00:00')
            ->max('2024-12-31T23:59');

        $rules = $field->getValidationRules();

        $this->assertContains('date', $rules);
        $this->assertContains('after_or_equal:2024-01-01T00:00', $rules);
        $this->assertContains('before_or_equal:2024-12-31T23:59', $rules);
    }

    /**
     * @test
     */
    public function datetime_field_fluent_interface(): void
    {
        $field = new DateTimeField('appointment');

        $result = $field
            ->datetime()
            ->withSeconds()
            ->min('2024-01-01T00:00:00')
            ->max('2024-12-31T23:59:59')
            ->step(1);

        $this->assertEquals('datetime-local', $field->type);
        $this->assertTrue($field->withSeconds);
        $this->assertEquals('Y-m-d\TH:i:s', $field->format);
        $this->assertEquals('2024-01-01T00:00:00', $field->min);
        $this->assertEquals('2024-12-31T23:59:59', $field->max);
        $this->assertEquals(1, $field->step);
        $this->assertSame($field, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     *
     * @test
     */
    public function datetime_field_render(): void
    {
        $field = new DateTimeField('appointment');
        $field->min('2024-01-01T00:00')->max('2024-12-31T23:59');

        $rendered = $field->render();

        $this->assertStringContainsString('name="appointment"', $rendered);
        $this->assertStringContainsString('type="datetime-local"', $rendered);
    }
}
