<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form\Concerns;

use Milenmk\LaravelSimpleDatatablesAndForms\Form\Concerns\Set;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;

class SetTest extends BaseTest
{
    #[Test]
    public function set_creation_with_empty_data(): void
    {
        $formData = [];
        $set = new Set($formData);

        $this->assertEquals([], $set->getPendingUpdates());
    }

    #[Test]
    public function set_creation_with_initial_data(): void
    {
        $formData = ['name' => 'John', 'email' => 'john@example.com'];
        $set = new Set($formData);

        $this->assertEquals([], $set->getPendingUpdates());
    }

    #[Test]
    public function set_invoke_method(): void
    {
        $formData = [];
        $set = new Set($formData);

        $set('name', 'John');
        $set('email', 'john@example.com');

        $this->assertEquals('John', $formData['name']);
        $this->assertEquals('john@example.com', $formData['email']);
        $this->assertEquals(['name' => 'John', 'email' => 'john@example.com'], $set->getPendingUpdates());
    }

    #[Test]
    public function set_method(): void
    {
        $formData = [];
        $set = new Set($formData);

        $set->set('name', 'Jane');
        $set->set('age', 25);

        $this->assertEquals('Jane', $formData['name']);
        $this->assertEquals(25, $formData['age']);
        $this->assertEquals(['name' => 'Jane', 'age' => 25], $set->getPendingUpdates());
    }

    #[Test]
    public function fill_method(): void
    {
        $formData = [];
        $set = new Set($formData);

        $data = ['name' => 'Bob', 'email' => 'bob@example.com', 'age' => 30];
        $set->fill($data);

        $this->assertEquals('Bob', $formData['name']);
        $this->assertEquals('bob@example.com', $formData['email']);
        $this->assertEquals(30, $formData['age']);
        $this->assertEquals($data, $set->getPendingUpdates());
    }

    #[Test]
    public function clear_method(): void
    {
        $formData = ['name' => 'John', 'email' => 'john@example.com'];
        $set = new Set($formData);

        $set->clear('name');

        $this->assertNull($formData['name']);
        $this->assertEquals('john@example.com', $formData['email']);
        $this->assertEquals(['name' => null], $set->getPendingUpdates());
    }

    #[Test]
    public function forget_method(): void
    {
        $formData = ['name' => 'John', 'email' => 'john@example.com'];
        $set = new Set($formData);

        $set->forget('name');

        $this->assertArrayNotHasKey('name', $formData);
        $this->assertEquals('john@example.com', $formData['email']);
        $this->assertEquals(['name' => null], $set->getPendingUpdates());
    }

    #[Test]
    public function pending_updates_tracking(): void
    {
        $formData = [];
        $set = new Set($formData);

        $this->assertEquals([], $set->getPendingUpdates());

        $set->set('name', 'John');
        $this->assertEquals(['name' => 'John'], $set->getPendingUpdates());

        $set->set('email', 'john@example.com');
        $this->assertEquals(['name' => 'John', 'email' => 'john@example.com'], $set->getPendingUpdates());

        $set->clearPendingUpdates();
        $this->assertEquals([], $set->getPendingUpdates());
    }

    #[Test]
    public function clear_pending_updates(): void
    {
        $formData = [];
        $set = new Set($formData);

        $set->set('name', 'John');
        $set->set('email', 'john@example.com');

        $this->assertNotEmpty($set->getPendingUpdates());

        $set->clearPendingUpdates();

        $this->assertEquals([], $set->getPendingUpdates());
        // Original form data should remain unchanged
        $this->assertEquals('John', $formData['name']);
        $this->assertEquals('john@example.com', $formData['email']);
    }

    #[Test]
    public function set_form_data_reference(): void
    {
        $originalData = ['name' => 'John'];
        $set = new Set($originalData);

        $set->set('email', 'john@example.com');
        $this->assertEquals('john@example.com', $originalData['email']);

        $newData = ['name' => 'Jane'];
        $set->setFormDataReference($newData);

        $set->set('age', 25);
        $this->assertEquals(25, $newData['age']);
        // Original data should not be affected
        $this->assertArrayNotHasKey('age', $originalData);
    }

    #[Test]
    public function reference_behavior(): void
    {
        $formData = ['name' => 'John'];
        $set = new Set($formData);

        // Modify through Set
        $set->set('email', 'john@example.com');

        // Should be reflected in original array
        $this->assertEquals('john@example.com', $formData['email']);

        // Set should work with the updated array
        $set->set('city', 'New York');
        $this->assertEquals('New York', $formData['city']);
    }

    #[Test]
    public function overwriting_values(): void
    {
        $formData = ['name' => 'John'];
        $set = new Set($formData);

        $set->set('name', 'Jane');
        $this->assertEquals('Jane', $formData['name']);

        $set->set('name', 'Bob');

        // Pending updates should track the latest value
        $this->assertEquals(['name' => 'Bob'], $set->getPendingUpdates());
    }
}
