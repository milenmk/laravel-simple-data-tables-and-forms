<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form;

use Illuminate\Database\Eloquent\Model;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\SelectField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Form;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Sections\Section;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class FormTest extends BaseTest
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_be_created()
    {
        $form = new Form;

        $this->assertEquals('', $form->heading);
        $this->assertEquals(2, $form->columns);
        $this->assertEquals('light', $form->theme);
        $this->assertEquals([], $form->extraAttributes);
    }

    #[Test]
    public function it_can_set_schema()
    {
        $form = new Form;
        $fields = [InputField::make('name'), InputField::make('email')];

        $result = $form->schema($fields);

        $this->assertEquals($fields, $form->getFields());
        $this->assertSame($form, $result);
    }

    #[Test]
    public function it_can_get_fields()
    {
        $form = new Form;
        $fields = [InputField::make('name'), InputField::make('email')];

        $form->schema($fields);

        $this->assertEquals($fields, $form->getFields());
    }

    #[Test]
    public function it_can_set_sections()
    {
        $form = new Form;
        $sections = [
            Section::make('personal')->fields([InputField::make('name'), InputField::make('email')]),
            Section::make('address')->fields([InputField::make('street'), InputField::make('city')]),
        ];

        $result = $form->sections($sections);

        $this->assertSame($form, $result);
    }

    #[Test]
    public function it_can_set_heading_as_string()
    {
        $form = new Form;
        $result = $form->heading('User Form');

        $this->assertEquals('User Form', $form->heading);
        $this->assertSame($form, $result);
    }

    #[Test]
    public function it_can_set_heading_as_array()
    {
        $form = new Form;
        $headings = ['en' => 'User Form', 'es' => 'Formulario de Usuario'];

        $result = $form->heading($headings);

        $this->assertEquals($headings, $form->heading);
        $this->assertSame($form, $result);
    }

    #[Test]
    public function it_can_set_columns()
    {
        $form = new Form;
        $result = $form->columns(3);

        $this->assertEquals(3, $form->columns);
        $this->assertSame($form, $result);
    }

    #[Test]
    public function it_can_set_theme()
    {
        $form = new Form;
        $result = $form->theme('dark');

        $this->assertEquals('dark', $form->theme);
        $this->assertSame($form, $result);
    }

    #[Test]
    public function it_can_set_model_class()
    {
        $form = new Form;
        $result = $form->model('App\\Models\\User');

        $this->assertSame($form, $result);
    }

    #[Test]
    public function it_can_set_extra_attributes_as_array()
    {
        $form = new Form;
        $attributes = ['class' => 'custom-form', 'data-test' => 'value'];

        $result = $form->extraAttributes($attributes);

        $this->assertEquals($attributes, $form->extraAttributes);
        $this->assertSame($form, $result);
    }

    #[Test]
    public function it_can_set_extra_attributes_as_closure()
    {
        $form = new Form;
        $closure = fn () => ['class' => 'dynamic-form'];

        $result = $form->extraAttributes($closure);

        $this->assertEquals($closure, $form->extraAttributesCallback);
        $this->assertSame($form, $result);
    }

    #[Test]
    public function it_can_get_merged_attributes()
    {
        $form = new Form;
        $form->extraAttributes(['class' => 'extra-class', 'data-test' => 'value']);

        $baseAttributes = ['class' => 'base-class', 'id' => 'test-id'];
        $merged = $form->getMergedAttributes($baseAttributes);

        $this->assertEquals('base-class extra-class', $merged['class']);
        $this->assertEquals('test-id', $merged['id']);
        $this->assertEquals('value', $merged['data-test']);
    }

    #[Test]
    public function it_can_get_extra_attributes()
    {
        $form = new Form;
        $attributes = ['class' => 'test-class'];
        $form->extraAttributes($attributes);

        $this->assertEquals($attributes, $form->getExtraAttributes());
    }

    #[Test]
    public function it_can_get_extra_attributes_with_callback()
    {
        $form = new Form;
        $form->extraAttributes(fn () => ['class' => 'callback-class']);

        $attributes = $form->getExtraAttributes();

        $this->assertEquals('callback-class', $attributes['class']);
    }

    #[Test]
    public function it_can_set_model_and_fill_data_separately()
    {
        $form = new Form;
        $fields = [InputField::make('name'), SelectField::make('category')];
        $form->schema($fields);

        // Create a mock model
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('toArray')->andReturn(['id' => 1, 'name' => 'Test']);

        $formData = ['name' => 'Test Name'];

        // Test setting model and filling data separately
        $result1 = $form->setModel($model);
        $result2 = $form->fill($formData);

        $this->assertSame($form, $result1);
        $this->assertSame($form, $result2);
        $this->assertSame($model, $form->getModel());
        $this->assertEquals($formData, $form->getFormData());
    }

    #[Test]
    public function it_can_fill_form_data()
    {
        $form = new Form;
        $data = ['name' => 'John', 'email' => 'john@example.com'];

        $result = $form->fill($data);

        $this->assertSame($form, $result);
    }

    #[Test]
    public function it_can_get_form_data()
    {
        $form = new Form;
        $data = ['name' => 'John', 'email' => 'john@example.com'];
        $form->fill($data);

        $this->assertEquals($data, $form->getFormData());
    }

    #[Test]
    public function it_can_set_and_get_sections()
    {
        $form = new Form;
        $sections = [
            Section::make('Personal Info')->fields([InputField::make('name'), InputField::make('email')]),
            Section::make('Address')->fields([InputField::make('street'), InputField::make('city')]),
        ];

        $result = $form->sections($sections);

        $this->assertSame($form, $result);
        $this->assertCount(2, $form->getSections());
        $this->assertInstanceOf(Section::class, $form->getSections()[0]);
    }

    #[Test]
    public function it_can_create_sections_from_arrays()
    {
        $form = new Form;
        $sections = [['Personal Info', [InputField::make('name')]], ['Address', [InputField::make('street')]]];

        $form->sections($sections);

        $this->assertCount(2, $form->getSections());
        $this->assertInstanceOf(Section::class, $form->getSections()[0]);
    }

    #[Test]
    public function it_can_set_and_get_columns()
    {
        $form = new Form;

        $result = $form->columns(3);

        $this->assertSame($form, $result);
        $this->assertEquals(3, $form->getColumns());
    }

    #[Test]
    public function it_has_default_columns()
    {
        $form = new Form;

        $this->assertEquals(2, $form->getColumns());
    }

    #[Test]
    public function it_can_set_and_get_model_class()
    {
        $form = new Form;
        $modelClass = 'App\\Models\\User';

        $result = $form->model($modelClass);

        $this->assertSame($form, $result);
        $this->assertEquals($modelClass, $form->getModelClass());
    }

    #[Test]
    public function it_can_fill_from_model()
    {
        $form = new Form;
        $model = Mockery::mock(Model::class);
        $modelData = ['name' => 'John', 'email' => 'john@example.com'];
        $model->shouldReceive('toArray')->andReturn($modelData);

        $result = $form->fillFromModel($model);

        $this->assertSame($form, $result);
        $this->assertSame($model, $form->getModel());
        $this->assertEquals($modelData, $form->getFormData());
    }

    #[Test]
    public function it_handles_null_model_in_fill_from_model()
    {
        $form = new Form;

        $result = $form->fillFromModel(null);

        $this->assertSame($form, $result);
        $this->assertNull($form->getModel());
        $this->assertEquals([], $form->getFormData());
    }

    #[Test]
    public function it_merges_form_data_when_filling()
    {
        $form = new Form;
        $initialData = ['name' => 'John'];
        $additionalData = ['email' => 'john@example.com'];

        $form->fill($initialData);
        $form->fill($additionalData);

        $expected = array_merge($initialData, $additionalData);
        $this->assertEquals($expected, $form->getFormData());
    }

    #[Test]
    public function it_can_get_validation_rules_for_fields()
    {
        $form = new Form;
        $fields = [
            InputField::make('name')->rules(['required', 'string', 'max:255']),
            InputField::make('email')
                ->rules(['required', 'email'])
                ->required(),
            InputField::make('age')->rules(['integer', 'min:18']),
        ];
        $form->schema($fields);

        $rules = $form->getValidationRules();

        $this->assertArrayHasKey('formData.name', $rules);
        $this->assertArrayHasKey('formData.email', $rules);
        $this->assertArrayHasKey('formData.age', $rules);
        $this->assertContains('required', $rules['formData.name']);
        $this->assertContains('required', $rules['formData.email']);
    }

    #[Test]
    public function it_sets_model_class_on_select_fields()
    {
        $form = new Form;
        $selectField = Mockery::mock(SelectField::class);
        $selectField
            ->shouldReceive('setFormModelClass')
            ->with('App\\Models\\User')
            ->once();

        $fields = [InputField::make('name'), $selectField];

        $form->schema($fields);
        $result = $form->model('App\\Models\\User');

        $this->assertSame($form, $result);
        $this->assertEquals('App\\Models\\User', $form->getModelClass());
    }

    #[Test]
    public function it_sets_model_class_on_select_fields_in_sections()
    {
        $form = new Form;
        $selectField = Mockery::mock(SelectField::class);
        $selectField
            ->shouldReceive('setFormModelClass')
            ->with('App\\Models\\User')
            ->once();

        $section = Mockery::mock(Section::class);
        $section->shouldReceive('getFields')->andReturn([$selectField]);

        $form->sections([$section]);
        $result = $form->model('App\\Models\\User');

        $this->assertSame($form, $result);
        $this->assertEquals('App\\Models\\User', $form->getModelClass());
    }
}
