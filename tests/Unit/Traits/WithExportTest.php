<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\ExportService;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\SearchService;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\WithExport;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WithExportTest extends BaseTest
{
    private object $component;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test component that uses the WithExport trait
        $this->component = new class
        {
            use WithExport;

            public string $search = '';
            public array $filters = [];
            public string $sortField = '';
            public string $sortDir = 'asc';

            public function dispatch(string $event, array $data = []): void
            {
                // Mock dispatch method
            }

            public function table(Table $table): void
            {
                // Mock table method
            }
        };
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function export_returns_null_when_disabled_in_config(): void
    {
        Config::set('simple-datatables-and-forms.export.enable', false);

        $result = $this->component->export();

        $this->assertNull($result);
    }

    #[Test]
    public function export_uses_default_format_when_invalid_format_provided(): void
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
        $exportService->shouldReceive('toCsv')->andReturn(new StreamedResponse);
        $this->app->instance(ExportService::class, $exportService);

        // Mock Table
        $table = Mockery::mock(Table::class);
        $table->shouldReceive('getQuery')->andReturn(Mockery::mock(Builder::class));
        $this->app->instance(Table::class, $table);

        $result = $this->component->export('invalid_format');

        $this->assertInstanceOf(StreamedResponse::class, $result);
    }

    #[Test]
    public function export_returns_null_when_required_package_not_available(): void
    {
        Config::set('simple-datatables-and-forms.export.enable', true);

        Config::set('simple-datatables-and-forms.export.formats', ['csv', 'excel', 'pdf']);

        // Mock ExportService
        $exportService = Mockery::mock(ExportService::class);
        $exportService
            ->shouldReceive('isPackageAvailable')
            ->with('pdf')
            ->andReturn(false);
        $exportService
            ->shouldReceive('getRequiredPackage')
            ->with('pdf')
            ->andReturn('barryvdh/laravel-dompdf');
        $this->app->instance(ExportService::class, $exportService);

        $result = $this->component->export('pdf');

        $this->assertNull($result);
    }

    #[Test]
    public function export_csv_success(): void
    {
        Config::set('simple-datatables-and-forms.export.enable', true);

        Config::set('simple-datatables-and-forms.export.formats', ['csv', 'excel', 'pdf']);

        // Mock ExportService
        $exportService = Mockery::mock(ExportService::class);
        $exportService
            ->shouldReceive('isPackageAvailable')
            ->with('csv')
            ->andReturn(true);
        $exportService->shouldReceive('toCsv')->andReturn(new StreamedResponse);
        $this->app->instance(ExportService::class, $exportService);

        // Mock Table
        $table = Mockery::mock(Table::class);
        $table->shouldReceive('getQuery')->andReturn(Mockery::mock(Builder::class));
        $this->app->instance(Table::class, $table);

        $result = $this->component->export();

        $this->assertInstanceOf(StreamedResponse::class, $result);
    }

    #[Test]
    public function export_excel_success(): void
    {
        Config::set('simple-datatables-and-forms.export.enable', true);

        Config::set('simple-datatables-and-forms.export.formats', ['csv', 'excel', 'pdf']);

        // Mock ExportService
        $exportService = Mockery::mock(ExportService::class);
        $exportService
            ->shouldReceive('isPackageAvailable')
            ->with('excel')
            ->andReturn(true);
        $exportService->shouldReceive('toExcel')->andReturn(new StreamedResponse);
        $this->app->instance(ExportService::class, $exportService);

        // Mock Table
        $table = Mockery::mock(Table::class);
        $table->shouldReceive('getQuery')->andReturn(Mockery::mock(Builder::class));
        $this->app->instance(Table::class, $table);

        $result = $this->component->export('excel');

        $this->assertInstanceOf(StreamedResponse::class, $result);
    }

    #[Test]
    public function export_filename_generation(): void
    {
        Config::set('simple-datatables-and-forms.export.filename_prefix', 'test_export');

        $filename = $this->component->exportFilename('csv');

        $this->assertStringStartsWith('test_export_', $filename);
        $this->assertStringEndsWith('.csv', $filename);
        $this->assertStringContainsString(class_basename($this->component), $filename);
        $this->assertMatchesRegularExpression('/test_export_.*_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.csv/', $filename);
    }

    #[Test]
    public function export_applies_search_when_present(): void
    {
        Config::set('simple-datatables-and-forms.export.enable', true);

        Config::set('simple-datatables-and-forms.export.formats', ['csv', 'excel', 'pdf']);

        // Set search term
        $this->component->search = 'test search';

        // Mock services
        $exportService = Mockery::mock(ExportService::class);
        $exportService
            ->shouldReceive('isPackageAvailable')
            ->with('csv')
            ->andReturn(true);
        $exportService->shouldReceive('toCsv')->andReturn(new StreamedResponse);
        $this->app->instance(ExportService::class, $exportService);

        $searchService = Mockery::mock(SearchService::class);
        $query = Mockery::mock(Builder::class);
        $searchService
            ->shouldReceive('applySearch')
            ->with($query, 'test search', Mockery::any())
            ->andReturn($query);
        $this->app->instance(SearchService::class, $searchService);

        $table = Mockery::mock(Table::class);
        $table->shouldReceive('getQuery')->andReturn($query);
        $this->app->instance(Table::class, $table);

        $result = $this->component->export();

        $this->assertInstanceOf(StreamedResponse::class, $result);
    }
}
