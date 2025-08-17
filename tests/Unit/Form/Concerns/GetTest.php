<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form\Concerns;

use Milenmk\LaravelSimpleDatatablesAndForms\Form\Concerns\Get;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;

class GetTest extends BaseTest
{
    /**
     * @test
     */
    public function get_creation_with_empty_data(): void
    {
        $get = new Get;

        $this->assertEquals([], $get->all());
    }

    /**
     * @test
     */
    public function get_creation_with_initial_data(): void
    {
        $data = ['name' => 'John', 'email' => 'john@example.com'];
        $get = new Get($data);

        $this->assertEquals($data, $get->all());
    }

    /**
     * @test
     */
    public function get_invoke_method(): void
    {
        $data = ['name' => 'John', 'email' => 'john@example.com'];
        $get = new Get($data);

        $this->assertEquals('John', $get('name'));
        $this->assertEquals('john@example.com', $get('email'));
        $this->assertNull($get('nonexistent'));
    }

    /**
     * @test
     */
    public function get_method(): void
    {
        $data = ['name' => 'John', 'email' => 'john@example.com'];
        $get = new Get($data);

        $this->assertEquals('John', $get->get('name'));
        $this->assertEquals('john@example.com', $get->get('email'));
        $this->assertNull($get->get('nonexistent'));
    }

    /**
     * @test
     */
    public function set_form_data(): void
    {
        $get = new Get;
        $data = ['name' => 'Jane', 'age' => 25];

        $get->setFormData($data);

        $this->assertEquals($data, $get->all());
        $this->assertEquals('Jane', $get->get('name'));
        $this->assertEquals(25, $get->get('age'));
    }

    /**
     * @test
     */
    public function has_method(): void
    {
        $data = ['name' => 'John', 'email' => null, 'active' => false];
        $get = new Get($data);

        $this->assertTrue($get->has('name'));
        $this->assertTrue($get->has('email')); // null value but key exists
        $this->assertTrue($get->has('active')); // false value but key exists
        $this->assertFalse($get->has('nonexistent'));
    }

    /**
     * @test
     */
    public function is_empty_method(): void
    {
        $data = [
            'name' => 'John',
            'empty_string' => '',
            'null_value' => null,
            'zero' => 0,
            'false_value' => false,
            'empty_array' => [],
        ];
        $get = new Get($data);

        $this->assertFalse($get->isEmpty('name'));
        $this->assertTrue($get->isEmpty('empty_string'));
        $this->assertTrue($get->isEmpty('null_value'));
        $this->assertTrue($get->isEmpty('zero'));
        $this->assertTrue($get->isEmpty('false_value'));
        $this->assertTrue($get->isEmpty('empty_array'));
        $this->assertTrue($get->isEmpty('nonexistent'));
    }

    /**
     * @test
     */
    public function is_not_empty_method(): void
    {
        $data = [
            'name' => 'John',
            'empty_string' => '',
            'null_value' => null,
        ];
        $get = new Get($data);

        $this->assertTrue($get->isNotEmpty('name'));
        $this->assertFalse($get->isNotEmpty('empty_string'));
        $this->assertFalse($get->isNotEmpty('null_value'));
        $this->assertFalse($get->isNotEmpty('nonexistent'));
    }

    /**
     * @test
     */
    public function all_method(): void
    {
        $data = ['name' => 'John', 'email' => 'john@example.com', 'age' => 30];
        $get = new Get($data);

        $this->assertEquals($data, $get->all());
    }

    /**
     * @test
     */
    public function updating_form_data(): void
    {
        $initialData = ['name' => 'John'];
        $get = new Get($initialData);

        $this->assertEquals('John', $get->get('name'));
        $this->assertNull($get->get('email'));

        $newData = ['name' => 'Jane', 'email' => 'jane@example.com'];
        $get->setFormData($newData);

        $this->assertEquals('Jane', $get->get('name'));
        $this->assertEquals('jane@example.com', $get->get('email'));
    }
}
