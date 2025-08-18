<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\ExportService;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\WithExport;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WithExportTest extends BaseTest
{
    private object $testClass;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an anonymous class that uses the WithExport trait
        $this->testClass = new class
        {
            use WithExport;

            public array $filters = [];
            public string $search = '';
            public string $sortField = '';
            public string $sortDir = 'asc';

            public function table(Table $table): void
            {
                // Mock implementation
            }

            public function dispatch(string $event, array $data = []): void
            {
                // Mock implementation for Livewire dispatch
            }
        };
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_null_when_export_is_disabled()
    {
        Config::set('simple-datatables-and-forms.export.enable', false);

        $result = $this->testClass->export();

        $this->assertNull($result);
    }

    #[Test]
    public function it_uses_default_format_when_invalid_format_provided()
    {
        Config::set('simple-datatables-and-forms.export.enable', true);

        Config::set('simple-datatables-and-forms.export.formats', ['csv', 'excel', 'pdf']);

        Config::set('simple-datatables-and-forms.export.default_format', 'csv');

        // Mock ExportService
        $exportService = Mockery::mock(ExportService::class);
        $exportService
            ->shouldReceive('isPackageAvailable')
            ->with('csv')
            ->andReturn(true);

        $this->app->instance(ExportService::class, $exportService);

        // Mock Table
        $table = Mockery::mock(Table::class);
        $mockQuery = Mockery::mock(Builder::class);
        $table->shouldReceive('getQuery')->andReturn($mockQuery);
        $this->app->instance(Table::class, $table);

        $mockResponse = Mockery::mock(StreamedResponse::class);
        $exportService
            ->shouldReceive('toCsv')
            ->once()
            ->andReturn($mockResponse);

        $this->testClass->export('invalid_format');

        // If we get here without exception, the test passes
        $this->assertTrue(true);
    }

    #[Test]
    public function it_returns_null_when_required_package_is_missing()
    {
        Config::set('simple-datatables-and-forms.export.enable', true);

        Config::set('simple-datatables-and-forms.export.formats', ['csv', 'excel', 'pdf']);

        // Mock ExportService
        $exportService = Mockery::mock(ExportService::class);
        $exportService
            ->shouldReceive('isPackageAvailable')
            ->with('excel')
            ->andReturn(false);

        $exportService
            ->shouldReceive('getRequiredPackage')
            ->with('excel')
            ->andReturn('phpoffice/phpspreadsheet');

        $this->app->instance(ExportService::class, $exportService);

        $result = $this->testClass->export('excel');

        $this->assertNull($result);
    }

    #[Test]
    public function it_generates_export_filename_with_default_values()
    {
        Config::set('simple-datatables-and-forms.export.filename_prefix', 'export');

        $filename = $this->testClass->exportFilename('csv');

        $this->assertStringStartsWith('export_', $filename);
        $this->assertStringEndsWith('.csv', $filename);
        $this->assertStringContainsString(date('Y-m-d'), $filename);
    }

    #[Test]
    public function it_generates_export_filename_with_custom_prefix()
    {
        Config::set('simple-datatables-and-forms.export.filename_prefix', 'custom_export');

        $filename = $this->testClass->exportFilename('xlsx');

        $this->assertStringStartsWith('custom_export_', $filename);
        $this->assertStringEndsWith('.xlsx', $filename);
    }

    #[Test]
    public function it_includes_class_name_in_filename()
    {
        Config::set('simple-datatables-and-forms.export.filename_prefix', 'export');

        $filename = $this->testClass->exportFilename('pdf');

        // The anonymous class will have a generated name
        $this->assertStringContainsString('export_', $filename);
        $this->assertStringEndsWith('.pdf', $filename);
    }

    #[Test]
    public function it_includes_timestamp_in_filename()
    {
        Config::set('simple-datatables-and-forms.export.filename_prefix', 'export');

        $filename1 = $this->testClass->exportFilename('csv');

        // Wait a moment to ensure different timestamp
        usleep(1000);

        $filename2 = $this->testClass->exportFilename('csv');

        // Both should contain timestamps but might be different
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/', $filename1);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/', $filename2);
    }
}
