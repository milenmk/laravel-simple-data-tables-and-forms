<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form\Fields;

use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\HiddenField;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Throwable;

class HiddenFieldTest extends BaseTest
{
    /**
     * @test
     */
    public function hidden_field_creation(): void
    {
        $field = new HiddenField('user_id');

        $this->assertEquals('user_id', $field->name);
        $this->assertEquals('hidden', $field->type);
    }

    /**
     * @throws Throwable
     *
     * @test
     */
    public function hidden_field_render(): void
    {
        $field = new HiddenField('user_id');

        $rendered = $field->render();

        $this->assertStringContainsString('type="hidden"', $rendered);
        $this->assertStringContainsString('name="user_id"', $rendered);
    }

    /**
     * @throws Throwable
     *
     * @test
     */
    public function hidden_field_render_without_value(): void
    {
        $field = new HiddenField('token');

        $rendered = $field->render();

        $this->assertStringContainsString('type="hidden"', $rendered);
        $this->assertStringContainsString('name="token"', $rendered);
    }
}
