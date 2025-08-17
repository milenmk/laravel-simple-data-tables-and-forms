<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form\Fields;

use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\TextareaField;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class TextareaFieldTest extends BaseTest
{
    /**
     * @test
     */
    public function textarea_field_creation(): void
    {
        $field = new TextareaField('description');

        $this->assertEquals('description', $field->name);
        $this->assertEquals(3, $field->rows);
        $this->assertEquals(50, $field->cols);
        $this->assertNull($field->minLength);
        $this->assertNull($field->maxLength);
        $this->assertFalse($field->autosize);
    }

    /**
     * @test
     */
    public function textarea_field_rows_configuration(): void
    {
        $field = new TextareaField('description');
        $result = $field->rows(5);

        $this->assertEquals(5, $field->rows);
        $this->assertSame($field, $result); // Test fluent interface
    }

    /**
     * @test
     */
    public function textarea_field_cols_configuration(): void
    {
        $field = new TextareaField('description');
        $result = $field->cols(80);

        $this->assertEquals(80, $field->cols);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function textarea_field_min_length_configuration(): void
    {
        $field = new TextareaField('description');
        $result = $field->minLength(10);

        $this->assertEquals(10, $field->minLength);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function textarea_field_max_length_configuration(): void
    {
        $field = new TextareaField('description');
        $result = $field->maxLength(500);

        $this->assertEquals(500, $field->maxLength);
        $this->assertSame($field, $result);
    }

    /**
     * @test
     */
    public function textarea_field_autosize_configuration(): void
    {
        $field = new TextareaField('description');

        // Test enabling autosize
        $result = $field->autosize();
        $this->assertTrue($field->autosize);
        $this->assertSame($field, $result);

        // Test disabling autosize
        $field->autosize(false);
        $this->assertFalse($field->autosize);

        // Test default parameter (true)
        $field->autosize();
        $this->assertTrue($field->autosize);
    }

    /**
     * @test
     */
    public function textarea_field_fluent_interface(): void
    {
        $field = new TextareaField('description');

        $result = $field
            ->rows(4)
            ->cols(60)
            ->minLength(5)
            ->maxLength(1000)
            ->autosize();

        $this->assertEquals(4, $field->rows);
        $this->assertEquals(60, $field->cols);
        $this->assertEquals(5, $field->minLength);
        $this->assertEquals(1000, $field->maxLength);
        $this->assertTrue($field->autosize);
        $this->assertSame($field, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     *
     * @test
     */
    public function textarea_field_render(): void
    {
        $field = new TextareaField('description');
        $field
            ->rows(4)
            ->cols(60)
            ->minLength(5)
            ->maxLength(1000);

        $rendered = $field->render();

        $this->assertStringContainsString('name="description"', $rendered);
        $this->assertStringContainsString('rows="4"', $rendered);
        $this->assertStringContainsString('cols="60"', $rendered);
    }
}
