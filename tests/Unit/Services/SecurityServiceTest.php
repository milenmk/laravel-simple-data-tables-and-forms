<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Services;

use Milenmk\LaravelSimpleDatatablesAndForms\Services\SecurityService;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;

class SecurityServiceTest extends BaseTest
{
    protected SecurityService $securityService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->securityService = new SecurityService;
    }

    #[Test]
    public function it_can_sanitize_input()
    {
        $input = '<script>alert("XSS")</script>Test Input';

        $sanitized = $this->securityService->sanitizeInput($input);

        $this->assertEquals('Test Input', $sanitized);
    }

    #[Test]
    public function it_can_validate_sort_field()
    {
        $allowedFields = ['name', 'email', 'created_at'];

        // Valid field
        $this->assertTrue($this->securityService->validateSortField('name', $allowedFields));

        // Invalid field
        $this->assertFalse($this->securityService->validateSortField('password', $allowedFields));

        // SQL injection attempt
        $this->assertFalse($this->securityService->validateSortField('name; DROP TABLE users;', $allowedFields));
    }

    #[Test]
    public function it_can_validate_sort_direction()
    {
        // Valid directions
        $this->assertTrue($this->securityService->validateSortDirection('ASC'));
        $this->assertTrue($this->securityService->validateSortDirection('DESC'));

        // Invalid directions
        $this->assertFalse($this->securityService->validateSortDirection('RANDOM'));
        $this->assertFalse($this->securityService->validateSortDirection('asc; DROP TABLE users;'));
    }

    #[Test]
    public function it_can_get_csrf_field()
    {
        $csrfField = $this->securityService->getCsrfField();

        $this->assertStringContainsString('csrf-token', $csrfField);
    }
}
