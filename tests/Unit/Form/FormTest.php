<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form;

use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Form;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Sections\Section;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

class FormTest extends BaseTest
{
    /**
     * @test
     */
    #[Test]
    public function form_can_set_model()
    {
        $form = new Form;
        $form->model('App\\Models\\User');

        $this->assertEquals('App\\Models\\User', $form->getModelClass());
    }

    /**
     * @test
     */
    #[Test]
    public function form_can_set_columns()
    {
        $form = new Form;
        $form->columns(3);

        $this->assertEquals(3, $form->columns);
    }

    /**
     * @test
     */
    #[Test]
    public function form_can_set_heading()
    {
        $form = new Form;
        $heading = [
            'title' => 'Test Form',
            'description' => 'Test description',
        ];

        $form->heading($heading);

        $this->assertEquals($heading, $form->heading);
    }

    /**
     * @test
     */
    #[Test]
    public function form_can_add_fields()
    {
        $form = new Form;

        $fields = [InputField::make('name'), InputField::make('email')];

        $form->schema($fields);

        $fields = $form->getFields();
        $this->assertCount(2, $fields);
        $this->assertEquals('name', $fields[0]->name);
        $this->assertEquals('email', $fields[1]->name);
    }

    /**
     * @test
     */
    #[Test]
    public function form_can_add_sections()
    {
        $form = new Form;

        $section = Section::make('personal_info')
            ->label('Personal Information')
            ->fields([InputField::make('name'), InputField::make('email')]);

        $form->sections([$section]);

        $sections = $form->getSections();
        $this->assertCount(1, $sections);
        $this->assertEquals('personal_info', $sections[0]->name);
        $this->assertCount(2, $sections[0]->fields);
    }

    /**
     * @throws Throwable
     *
     * @test
     */
    #[Test]
    public function form_renders_without_errors()
    {
        $form = new Form;

        $form->schema([InputField::make('name')->label('Name'), InputField::make('email')->label('Email')]);

        $view = $form->render();

        // Test that it can be rendered to string
        $rendered = $view->render();
        $this->assertIsString($rendered);
        $this->assertStringContainsString('form-container', $rendered);
    }

    /**
     * @test
     */
    #[Test]
    public function form_can_set_theme()
    {
        $form = new Form;
        $form->theme('dark');

        $this->assertEquals('dark', $form->theme);
    }

    /**
     * @test
     */
    #[Test]
    public function form_validation_rules_are_collected()
    {
        $form = new Form;

        $form->schema([
            InputField::make('name')->required(),
            InputField::make('email')
                ->email()
                ->required(),
        ]);

        $rules = $form->getValidationRules();

        $this->assertArrayHasKey('formData.name', $rules);
        $this->assertArrayHasKey('formData.email', $rules);
        $this->assertContains('required', $rules['formData.name']);
        $this->assertContains('required', $rules['formData.email']);
    }

    /**
     * @test
     */
    #[Test]
    public function form_can_fill_data()
    {
        $form = new Form;

        $form->schema([InputField::make('name'), InputField::make('email')]);

        $data = ['name' => 'John Doe', 'email' => 'john@example.com'];
        $form->fill($data);

        $formData = $form->getFormData();
        $this->assertEquals('John Doe', $formData['name']);
        $this->assertEquals('john@example.com', $formData['email']);
    }
}
