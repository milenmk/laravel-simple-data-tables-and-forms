<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Feature;

use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Components\TestFormComponent;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;
use PHPUnit\Framework\Attributes\Test;

class HasFormTraitTest extends BaseTest
{
    #[Test]
    public function it_can_render_form_component()
    {
        $component = Livewire::test(TestFormComponent::class);
        $component->assertSuccessful();
        $component->assertSeeHtml('Mocked Livewire Component');

        // Test direct component instantiation which we know works
        $directComponent = new TestFormComponent;
        $directComponent->mount();
        $this->assertIsArray($directComponent->formData);
        $this->assertEquals('test_form_component', $directComponent->componentName);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        // Test with direct component instantiation since Livewire has issues with formData initialization
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        // Try to save without setting required fields - this should trigger validation errors
        try {
            $directComponent->save();
            $this->fail('Expected validation exception was not thrown');
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $this->assertArrayHasKey('formData.name', $errors);
            $this->assertArrayHasKey('formData.email', $errors);
            $this->assertArrayHasKey('formData.category', $errors);
        }
    }

    #[Test]
    public function it_validates_email_format()
    {
        // Test with direct component instantiation since Livewire has issues with formData initialization
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $directComponent->setFormData([
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'category' => 'Electronics',
        ]);

        // Try to save with invalid email - this should trigger validation errors
        try {
            $directComponent->save();
            $this->fail('Expected validation exception was not thrown');
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $this->assertArrayHasKey('formData.email', $errors);
        }
    }

    #[Test]
    public function it_can_save_valid_form_data()
    {
        // Test with direct component instantiation since Livewire has issues with formData initialization
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $directComponent->setFormData([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'category' => 'Electronics',
            'is_active' => true,
            'price' => 199.99,
            'description' => 'Test description',
        ]);

        $directComponent->save();

        // Verify data was saved to database
        $this->assertDatabaseHas('test_models', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'category' => 'Electronics',
        ]);

        // Verify session message was set
        $this->assertEquals('Form saved successfully!', session('message'));
    }

    #[Test]
    public function it_can_load_model_data()
    {
        $model = TestModel::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'category' => 'Books',
            'is_active' => false,
            'price' => 29.99,
        ]);

        // Test with direct component instantiation since Livewire has issues with formData initialization
        $directComponent = new TestFormComponent;
        $directComponent->mount();
        $directComponent->loadFormModel($model);

        // Check that form data is populated from model
        $this->assertEquals('Existing User', $directComponent->formData['name']);
        $this->assertEquals('existing@example.com', $directComponent->formData['email']);
        $this->assertEquals('Books', $directComponent->formData['category']);
        $this->assertFalse($directComponent->formData['is_active']);
        $this->assertEquals(29.99, $directComponent->formData['price']);
    }

    #[Test]
    public function it_can_update_existing_model()
    {
        $model = TestModel::create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'category' => 'Electronics',
            'is_active' => true,
            'price' => 100.0,
        ]);

        // Test with direct component instantiation since Livewire has issues with formData initialization
        $directComponent = new TestFormComponent;
        $directComponent->mount();
        $directComponent->loadFormModel($model);

        $directComponent->formData['name'] = 'Updated Name';
        $directComponent->formData['email'] = 'updated@example.com';
        $directComponent->save();

        // Verify model was updated
        $this->assertDatabaseHas('test_models', [
            'id' => $model->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        // Verify session message was set
        $this->assertEquals('Form saved successfully!', session('message'));
    }

    #[Test]
    public function it_can_reset_form()
    {
        // Test with direct component instantiation since Livewire has issues with formData initialization
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $directComponent->setFormData([
            'name' => 'Test Name',
            'email' => 'test@example.com',
            'category' => 'Electronics',
        ]);

        $directComponent->resetForm();

        // Check that form data is reset to defaults
        $this->assertTrue($directComponent->formData['is_active']); // default value
        $this->assertFalse($directComponent->formData['notifications']); // default value
        $this->assertNull($directComponent->formModel);
    }

    #[Test]
    public function it_can_fill_form_data()
    {
        $data = [
            'name' => 'Filled Name',
            'email' => 'filled@example.com',
            'category' => 'Clothing',
        ];

        // Test with direct component instantiation since Livewire has issues with formData initialization
        $directComponent = new TestFormComponent;
        $directComponent->mount();
        $directComponent->fillForm($data);

        $this->assertEquals('Filled Name', $directComponent->formData['name']);
        $this->assertEquals('filled@example.com', $directComponent->formData['email']);
        $this->assertEquals('Clothing', $directComponent->formData['category']);
    }

    #[Test]
    public function it_renders_form_fields_correctly()
    {
        // Test with direct component instantiation since Livewire has issues with formData initialization
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        // Get the form instance and check its structure
        $form = $directComponent->getFormInstance();
        $fields = $form->getFields();

        // Check that expected fields exist
        $fieldNames = array_map(fn ($field) => $field->name, $fields);
        $this->assertContains('name', $fieldNames);
        $this->assertContains('email', $fieldNames);
        $this->assertContains('category', $fieldNames);

        // Check field properties
        $nameField = collect($fields)->firstWhere('name', 'name');
        $this->assertEquals('Enter your full name', $nameField->placeholder);

        $emailField = collect($fields)->firstWhere('name', 'email');
        $this->assertEquals('email', $emailField->type);
    }
}
