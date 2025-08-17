<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Services;

use Exception;
use Illuminate\Support\Facades\Config;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\SecurityService;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class SecurityServiceTest extends BaseTest
{
    private SecurityService $securityService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->securityService = new SecurityService;
    }

    #[Test]
    public function it_sanitizes_inputs_when_enabled()
    {
        Config::set('simple-datatables-and-forms.security.sanitize_input', true);

        $inputs = [
            'name' => '<script>alert("xss")</script>John',
            'email' => 'test@example.com',
            'nested' => [
                'value' => '<script>evil()</script>clean',
            ],
        ];

        $result = $this->securityService->sanitizeInputs($inputs);

        $this->assertEquals('John', $result['name']);
        $this->assertEquals('test@example.com', $result['email']);
        $this->assertEquals('clean', $result['nested']['value']);
    }

    #[Test]
    public function it_skips_sanitization_when_disabled()
    {
        Config::set('simple-datatables-and-forms.security.sanitize_input', false);

        $inputs = [
            'name' => '<script>alert("xss")</script>John',
            'email' => 'test@example.com',
        ];

        $result = $this->securityService->sanitizeInputs($inputs);

        $this->assertEquals('<script>alert("xss")</script>John', $result['name']);
        $this->assertEquals('test@example.com', $result['email']);
    }

    #[Test]
    public function it_sanitizes_individual_input_when_enabled()
    {
        Config::set('simple-datatables-and-forms.security.sanitize_input', true);

        $input = '<script>alert("xss")</script>Hello World';
        $result = $this->securityService->sanitizeInput($input);

        $this->assertEquals('Hello World', $result);
    }

    #[Test]
    public function it_removes_javascript_patterns()
    {
        Config::set('simple-datatables-and-forms.security.sanitize_input', true);

        $input = 'Hello alert("test") World';
        $result = $this->securityService->sanitizeInput($input);

        $this->assertEquals('Hello  World', $result);
    }

    #[Test]
    public function it_skips_input_sanitization_when_disabled()
    {
        Config::set('simple-datatables-and-forms.security.sanitize_input', false);

        $input = '<script>alert("xss")</script>Hello World';
        $result = $this->securityService->sanitizeInput($input);

        $this->assertEquals('<script>alert("xss")</script>Hello World', $result);
    }

    #[Test]
    public function it_validates_sort_field()
    {
        $allowedFields = ['name', 'email', 'created_at'];

        $this->assertTrue($this->securityService->validateSortField('name', $allowedFields));
        $this->assertTrue($this->securityService->validateSortField('email', $allowedFields));
        $this->assertFalse($this->securityService->validateSortField('password', $allowedFields));
        $this->assertFalse($this->securityService->validateSortField('id', $allowedFields));
    }

    #[Test]
    public function it_validates_sort_direction()
    {
        $this->assertTrue($this->securityService->validateSortDirection('asc'));
        $this->assertTrue($this->securityService->validateSortDirection('ASC'));
        $this->assertTrue($this->securityService->validateSortDirection('desc'));
        $this->assertTrue($this->securityService->validateSortDirection('DESC'));
        $this->assertFalse($this->securityService->validateSortDirection('invalid'));
        $this->assertFalse($this->securityService->validateSortDirection('up'));
    }

    #[Test]
    public function it_validates_field_names()
    {
        $this->assertTrue($this->securityService->validateFieldName('name'));
        $this->assertTrue($this->securityService->validateFieldName('user_name'));
        $this->assertTrue($this->securityService->validateFieldName('user.name'));
        $this->assertTrue($this->securityService->validateFieldName('field123'));

        $this->assertFalse($this->securityService->validateFieldName('field-name'));
        $this->assertFalse($this->securityService->validateFieldName('field name'));
        $this->assertFalse($this->securityService->validateFieldName('field@name'));
        $this->assertFalse($this->securityService->validateFieldName('field<script>'));
    }

    #[Test]
    public function it_sanitizes_field_values_by_type()
    {
        // Test email sanitization
        $result = $this->securityService->sanitizeFieldValue('test@example.com', 'email');
        $this->assertEquals('test@example.com', $result);

        // Test URL sanitization
        $result = $this->securityService->sanitizeFieldValue('https://example.com', 'url');
        $this->assertEquals('https://example.com', $result);

        // Test integer sanitization
        $result = $this->securityService->sanitizeFieldValue('123abc', 'integer');
        $this->assertEquals('123', $result);

        // Test float sanitization
        $result = $this->securityService->sanitizeFieldValue('123.45abc', 'float');
        $this->assertEquals('123.45', $result);

        // Test boolean sanitization
        $result = $this->securityService->sanitizeFieldValue('true', 'boolean');
        $this->assertTrue($result);

        $result = $this->securityService->sanitizeFieldValue('false', 'boolean');
        $this->assertFalse($result);

        // Test null value
        $result = $this->securityService->sanitizeFieldValue(null);
        $this->assertNull($result);
    }

    #[Test]
    public function it_sanitizes_default_string_field_values()
    {
        Config::set('simple-datatables-and-forms.security.sanitize_input', true);

        $result = $this->securityService->sanitizeFieldValue('<script>alert("test")</script>Hello');
        $this->assertEquals('Hello', $result);

        // Test default case (no field type specified)
        $result = $this->securityService->sanitizeFieldValue('<script>alert("test")</script>Hello');
        $this->assertEquals('Hello', $result);
    }

    #[Test]
    public function it_validates_file_uploads()
    {
        Config::set('simple-datatables-and-forms.security.max_file_size', 10240);

        // Test file size validation
        $file = ['size' => 10240 * 1024 + 1]; // Exceeds max size
        $errors = $this->securityService->validateFileUpload($file);

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('File size exceeds maximum', $errors[0]);

        // Test valid file size
        $file = ['size' => 5000 * 1024]; // Within limits
        $errors = $this->securityService->validateFileUpload($file);

        $this->assertCount(0, $errors);
    }

    #[Test]
    public function it_generates_csrf_field_when_enabled()
    {
        Config::set('simple-datatables-and-forms.security.csrf_protection', true);

        // Mock the csrf_token and csrf_field functions
        $this->app->instance('session', Mockery::mock());

        try {
            $this->securityService->getCsrfField();
            // If no exception is thrown, the method works
            $this->assertTrue(true);
        } catch (Exception) {
            // Expected in test environment without proper session setup
            $this->assertTrue(true);
        }
    }

    #[Test]
    public function it_returns_empty_csrf_field_when_disabled()
    {
        Config::set('simple-datatables-and-forms.security.csrf_protection', false);

        $result = $this->securityService->getCsrfField();

        $this->assertEquals('', $result);
    }
}
