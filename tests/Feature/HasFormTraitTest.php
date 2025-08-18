<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Feature;

use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
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

    #[Test]
    public function it_can_get_form_data()
    {
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $directComponent->setFormData([
            'name' => 'Test Name',
            'email' => 'test@example.com',
        ]);

        $formData = $directComponent->getFormData();

        $this->assertIsArray($formData);
        $this->assertEquals('Test Name', $formData['name']);
        $this->assertEquals('test@example.com', $formData['email']);
    }

    #[Test]
    public function it_can_get_form_property()
    {
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $formView = $directComponent->getFormProperty();

        $this->assertInstanceOf(View::class, $formView);
    }

    #[Test]
    public function it_can_refresh_form()
    {
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $directComponent->refreshForm();

        // If we get here without exception, the test passes
        $this->assertTrue(true);
    }

    #[Test]
    public function it_can_get_form_listeners()
    {
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $listeners = $directComponent->getFormListeners();

        $this->assertIsArray($listeners);
        $this->assertArrayHasKey('refreshForm', $listeners);
        $this->assertEquals('refreshForm', $listeners['refreshForm']);
    }

    #[Test]
    public function it_can_handle_updated_form_data()
    {
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $directComponent->formData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $directComponent->updatedFormData();

        // Should sanitize and validate the data
        $this->assertEquals('Updated Name', $directComponent->formData['name']);
        $this->assertEquals('updated@example.com', $directComponent->formData['email']);
    }

    #[Test]
    public function it_can_debug_form_data()
    {
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $directComponent->setFormData([
            'name' => 'Debug Name',
            'email' => 'debug@example.com',
        ]);

        $debug = $directComponent->debugFormData();

        $this->assertIsArray($debug);
        $this->assertArrayHasKey('formData', $debug);
        $this->assertArrayHasKey('fieldNames', $debug);
        $this->assertArrayHasKey('fieldDefaults', $debug);
        $this->assertEquals('Debug Name', $debug['formData']['name']);
    }

    #[Test]
    public function it_can_get_form_fields_data()
    {
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $directComponent->setFormData([
            'name' => 'Field Name',
            'email' => 'field@example.com',
        ]);

        $fieldsData = $directComponent->getFormFieldsData();

        $this->assertIsArray($fieldsData);
        $this->assertEquals('Field Name', $fieldsData['name']);
        $this->assertEquals('field@example.com', $fieldsData['email']);
    }

    #[Test]
    public function it_can_get_fillable_form_data_without_model()
    {
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $directComponent->setFormData([
            'name' => 'Fillable Name',
            'email' => 'fillable@example.com',
        ]);

        $fillableData = $directComponent->getFillableFormData();

        $this->assertIsArray($fillableData);
        $this->assertEquals('Fillable Name', $fillableData['name']);
        $this->assertEquals('fillable@example.com', $fillableData['email']);
    }

    #[Test]
    public function it_can_get_fillable_form_data_with_model()
    {
        $model = TestModel::create([
            'name' => 'Model Name',
            'email' => 'model@example.com',
            'category' => 'Books',
            'is_active' => true,
            'price' => 29.99,
        ]);

        $directComponent = new TestFormComponent;
        $directComponent->mount();
        $directComponent->loadFormModel($model);

        $fillableData = $directComponent->getFillableFormData();

        $this->assertIsArray($fillableData);
        $this->assertEquals('Model Name', $fillableData['name']);
        $this->assertEquals('model@example.com', $fillableData['email']);
    }

    #[Test]
    public function it_handles_form_fields_with_missing_data()
    {
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        // Don't set any form data, so fields will use defaults
        $fieldsData = $directComponent->getFormFieldsData();

        $this->assertIsArray($fieldsData);
        // Should have default values for boolean fields
        $this->assertTrue($fieldsData['is_active']); // default true
        $this->assertFalse($fieldsData['notifications']); // default false
    }

    #[Test]
    public function it_filters_out_non_form_fields_when_setting_data()
    {
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $directComponent->setFormData([
            'name' => 'Valid Field',
            'email' => 'valid@example.com',
            'invalid_field' => 'Should be filtered out',
            'another_invalid' => 'Also filtered',
        ]);

        $this->assertEquals('Valid Field', $directComponent->formData['name']);
        $this->assertEquals('valid@example.com', $directComponent->formData['email']);
        $this->assertArrayNotHasKey('invalid_field', $directComponent->formData);
        $this->assertArrayNotHasKey('another_invalid', $directComponent->formData);
    }

    #[Test]
    public function it_filters_out_non_form_fields_when_filling_form()
    {
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $directComponent->fillForm([
            'name' => 'Valid Field',
            'email' => 'valid@example.com',
            'invalid_field' => 'Should be filtered out',
            'another_invalid' => 'Also filtered',
        ]);

        $this->assertEquals('Valid Field', $directComponent->formData['name']);
        $this->assertEquals('valid@example.com', $directComponent->formData['email']);
        $this->assertArrayNotHasKey('invalid_field', $directComponent->formData);
        $this->assertArrayNotHasKey('another_invalid', $directComponent->formData);
    }

    #[Test]
    public function it_removes_non_form_fields_on_updated_form_data()
    {
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        // Manually set invalid fields (simulating potential security issue)
        $directComponent->formData = [
            'name' => 'Valid Field',
            'email' => 'valid@example.com',
            'invalid_field' => 'Should be removed',
        ];

        $directComponent->updatedFormData();

        $this->assertEquals('Valid Field', $directComponent->formData['name']);
        $this->assertEquals('valid@example.com', $directComponent->formData['email']);
        $this->assertArrayNotHasKey('invalid_field', $directComponent->formData);
    }
}
