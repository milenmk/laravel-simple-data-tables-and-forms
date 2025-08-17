<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form;

use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Components\TestFormComponent;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use ReflectionException;

class FormLivewireTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        TestModel::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_active' => true,
            'category' => 'Electronics',
            'price' => 99.99,
        ]);
    }

    #[Test]
    public function it_can_mount_livewire_component_with_form()
    {
        // This test verifies that the Livewire component with HasForm trait can be mounted
        // We're not testing the actual rendering of the form here, just that the component initializes properly
        Livewire::test(TestFormComponent::class)->assertSuccessful();
    }

    #[Test]
    public function it_initializes_form_data_correctly()
    {
        // Test direct component instantiation (without Livewire)
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $this->assertIsArray($directComponent->formData, 'Direct component formData should be an array');
        $this->assertArrayHasKey('name', $directComponent->formData);
        $this->assertArrayHasKey('is_active', $directComponent->formData);
        $this->assertTrue($directComponent->formData['is_active']);
        $this->assertFalse($directComponent->formData['notifications']);

        // Test Livewire component
        $component = Livewire::test(TestFormComponent::class);
        $component->assertSet('formData', null);
        $component->assertSuccessful();
    }

    #[Test]
    public function it_can_load_model_into_form()
    {
        $model = TestModel::first();

        // Test with direct component instantiation since Livewire has issues with formData initialization
        $directComponent = new TestFormComponent;
        $directComponent->mount();
        $directComponent->loadFormModel($model);

        // Check that form data is populated from model
        $this->assertEquals($model->name, $directComponent->formData['name']);
        $this->assertEquals($model->email, $directComponent->formData['email']);
        $this->assertEquals($model->is_active, $directComponent->formData['is_active']);
        $this->assertEquals($model, $directComponent->formModel);
    }

    #[Test]
    public function it_can_fill_form_data()
    {
        // Test with direct component instantiation since Livewire has issues with formData initialization
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $testData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $directComponent->fillForm($testData);

        $this->assertEquals('John Doe', $directComponent->formData['name']);
        $this->assertEquals('john@example.com', $directComponent->formData['email']);
    }

    #[Test]
    public function it_can_reset_form_data()
    {
        // Test with direct component instantiation since Livewire has issues with formData initialization
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        // Set some data first
        $directComponent->setFormData([
            'name' => 'Test Name',
            'email' => 'test@email.com',
        ]);

        // Reset form
        $directComponent->resetForm();

        // Check that form data is reset to defaults
        $this->assertTrue($directComponent->formData['is_active']); // default value
        $this->assertFalse($directComponent->formData['notifications']); // default value
        $this->assertNull($directComponent->formModel);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function it_generates_validation_rules_from_form()
    {
        // Test with direct component instantiation since Livewire has issues with formData initialization
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        // Use reflection to access the protected method
        $reflection = new ReflectionClass($directComponent);
        $method = $reflection->getMethod('getValidationRulesFromForm');

        $rules = $method->invoke($directComponent);

        // Check that required fields have required rule
        $this->assertContains('required', $rules['formData.name']);
        $this->assertContains('required', $rules['formData.email']);
        $this->assertContains('required', $rules['formData.category']);

        // Check that email field has email rule
        $this->assertContains('email', $rules['formData.email']);
    }

    #[Test]
    public function it_validates_form_data_on_save()
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
    public function it_saves_valid_form_data()
    {
        // Test with direct component instantiation since Livewire has issues with formData initialization
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $directComponent->setFormData([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'category' => 'Electronics',
            'is_active' => true,
            'notifications' => false,
            'price' => 99.99,
            'description' => 'Test description',
        ]);

        $directComponent->save();

        // Check that model was created
        $this->assertDatabaseHas('test_models', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'category' => 'Electronics',
        ]);
    }

    #[Test]
    public function it_updates_existing_model_when_form_model_is_set()
    {
        $model = TestModel::first();

        // Test with direct component instantiation since Livewire has issues with formData initialization
        $directComponent = new TestFormComponent;
        $directComponent->mount();
        $directComponent->loadFormModel($model);

        $directComponent->formData['name'] = 'Updated Name';
        $directComponent->save();

        // Check that existing model was updated
        $this->assertDatabaseHas('test_models', [
            'id' => $model->id,
            'name' => 'Updated Name',
        ]);
    }

    #[Test]
    public function it_can_set_and_get_form_data()
    {
        // Test with direct component instantiation since Livewire has issues with formData initialization
        $directComponent = new TestFormComponent;
        $directComponent->mount();

        $testData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ];

        $directComponent->setFormData($testData);

        $formData = $directComponent->getFormData();
        $this->assertEquals('Test User', $formData['name']);
        $this->assertEquals('test@example.com', $formData['email']);
    }
}
