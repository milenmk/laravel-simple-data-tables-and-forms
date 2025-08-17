<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form\Fields;

use Livewire\Component;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\Repeater;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class RepeaterTest extends BaseTest
{
    #[Test]
    public function repeater_field_creation(): void
    {
        $field = new Repeater('items');

        $this->assertEquals('items', $field->name);
        $this->assertEquals([], $field->schema);
        $this->assertEquals(0, $field->minItems);
        $this->assertEquals(10, $field->maxItems);
        $this->assertNull($field->relationship);
        $this->assertFalse($field->collapsible);
        $this->assertTrue($field->cloneable);
        $this->assertTrue($field->deletable);
        $this->assertEquals('Add Item', $field->addLabel);
        $this->assertEquals('Delete', $field->deleteLabel);
    }

    #[Test]
    public function repeater_field_schema_configuration(): void
    {
        $field = new Repeater('items');
        $schema = [new InputField('name'), new InputField('email')];

        $result = $field->schema($schema);

        $this->assertEquals($schema, $field->schema);
        $this->assertSame($field, $result); // Test fluent interface
    }

    #[Test]
    public function repeater_field_min_items_configuration(): void
    {
        $field = new Repeater('items');
        $result = $field->minItems(2);

        $this->assertEquals(2, $field->minItems);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function repeater_field_max_items_configuration(): void
    {
        $field = new Repeater('items');
        $result = $field->maxItems(5);

        $this->assertEquals(5, $field->maxItems);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function repeater_field_relationship_configuration(): void
    {
        $field = new Repeater('items');
        $result = $field->relationship('addresses');

        $this->assertEquals('addresses', $field->relationship);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function repeater_field_collapsible_configuration(): void
    {
        $field = new Repeater('items');

        // Test enabling collapsible
        $result = $field->collapsible();
        $this->assertTrue($field->collapsible);
        $this->assertSame($field, $result);

        // Test disabling collapsible
        $field->collapsible(false);
        $this->assertFalse($field->collapsible);

        // Test default parameter (true)
        $field->collapsible();
        $this->assertTrue($field->collapsible);
    }

    #[Test]
    public function repeater_field_cloneable_configuration(): void
    {
        $field = new Repeater('items');

        // Test disabling cloneable
        $result = $field->cloneable(false);
        $this->assertFalse($field->cloneable);
        $this->assertSame($field, $result);

        // Test enabling cloneable
        $field->cloneable();
        $this->assertTrue($field->cloneable);

        // Test default parameter (true)
        $field->cloneable();
        $this->assertTrue($field->cloneable);
    }

    #[Test]
    public function repeater_field_deletable_configuration(): void
    {
        $field = new Repeater('items');

        // Test disabling deletable
        $result = $field->deletable(false);
        $this->assertFalse($field->deletable);
        $this->assertSame($field, $result);

        // Test enabling deletable
        $field->deletable();
        $this->assertTrue($field->deletable);

        // Test default parameter (true)
        $field->deletable();
        $this->assertTrue($field->deletable);
    }

    #[Test]
    public function repeater_field_add_label_configuration(): void
    {
        $field = new Repeater('items');
        $result = $field->addLabel('Add New Item');

        $this->assertEquals('Add New Item', $field->addLabel);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function repeater_field_delete_label_configuration(): void
    {
        $field = new Repeater('items');
        $result = $field->deleteLabel('Remove Item');

        $this->assertEquals('Remove Item', $field->deleteLabel);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function repeater_field_validation_rules_basic(): void
    {
        $field = new Repeater('items');
        $field->minItems(2)->maxItems(5);

        $rules = $field->getValidationRules();

        $this->assertContains('array', $rules);
        $this->assertContains('min:2', $rules);
        $this->assertContains('max:5', $rules);
    }

    #[Test]
    public function repeater_field_validation_rules_without_min_max(): void
    {
        $field = new Repeater('items');
        // Don't set minItems or maxItems

        $rules = $field->getValidationRules();

        $this->assertContains('array', $rules);
        $this->assertNotContains('min:0', $rules); // minItems is 0, so no min rule
        $this->assertContains('max:10', $rules); // maxItems is 10 by default
    }

    #[Test]
    public function repeater_field_validation_rules_with_schema(): void
    {
        $field = new Repeater('items');

        $nameField = new InputField('name');
        $nameField->required();

        $emailField = new InputField('email');
        $emailField->email();

        $field->schema([$nameField, $emailField]);

        $rules = $field->getValidationRules();

        $this->assertContains('array', $rules);
        $this->assertArrayHasKey('items.*.name', $rules);
        $this->assertArrayHasKey('items.*.email', $rules);

        // Check that nested field rules are included
        $nameRules = $rules['items.*.name'];
        $emailRules = $rules['items.*.email'];

        $this->assertContains('required', $nameRules);
        $this->assertContains('email', $emailRules);
    }

    #[Test]
    public function repeater_field_validation_rules_with_schema_without_validation(): void
    {
        $field = new Repeater('items');

        // Create a mock field without getValidationRules method
        $mockField = new class
        {
            public string $name = 'mock_field';
        };

        $field->schema([$mockField]);

        $rules = $field->getValidationRules();

        $this->assertContains('array', $rules);
        // Should not have rules for the mock field since it doesn't have getValidationRules method
        $this->assertArrayNotHasKey('items.*.mock_field', $rules);
    }

    #[Test]
    public function repeater_field_fluent_interface(): void
    {
        $field = new Repeater('addresses');
        $schema = [new InputField('street'), new InputField('city')];

        $result = $field
            ->schema($schema)
            ->minItems(1)
            ->maxItems(3)
            ->relationship('userAddresses')
            ->collapsible()
            ->cloneable(false)
            ->deletable()
            ->addLabel('Add Address')
            ->deleteLabel('Remove Address');

        $this->assertEquals($schema, $field->schema);
        $this->assertEquals(1, $field->minItems);
        $this->assertEquals(3, $field->maxItems);
        $this->assertEquals('userAddresses', $field->relationship);
        $this->assertTrue($field->collapsible);
        $this->assertFalse($field->cloneable);
        $this->assertTrue($field->deletable);
        $this->assertEquals('Add Address', $field->addLabel);
        $this->assertEquals('Remove Address', $field->deleteLabel);
        $this->assertSame($field, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function repeater_field_render(): void
    {
        $field = new Repeater('items');
        $field
            ->schema([new InputField('name')])
            ->minItems(1)
            ->maxItems(5);

        view()->share(
            '__livewire',
            new class extends Component
            {
                public function render(): string
                {
                    return '';
                }
            },
        );

        $rendered = $field->render();

        $this->assertStringContainsString('name="`formData.items', $rendered);
    }
}
