<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Feature;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\ExportService;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\WithExport;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportTest extends BaseTest
{
    /**
     * Test component that uses the WithExport trait
     */
    protected $testComponent;

    /**
     * Setup test environment
     *
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        TestModel::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'is_active' => true,
            'category' => 'Electronics',
            'price' => 199.99,
        ]);

        TestModel::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'is_active' => false,
            'category' => 'Clothing',
            'price' => 49.99,
        ]);

        // Create a test component that uses the WithExport trait
        $this->testComponent = new class
        {
            use WithExport;

            public function table(Table $table): Table
            {
                return $table->query(TestModel::query())->model(TestModel::class);
            }

            // Mock these properties that would normally be set by the HasTable trait
            public string $search = '';
            public string $sortField = '';
            public string $sortDir = 'asc';
            public array $filters = [];

            // Mock the applyFiltersToQuery method that would normally be provided by WithFilters trait
            public function applyFiltersToQuery(Builder $query): void
            {
                // Do nothing in the test
            }
        };
    }

    #[Test]
    public function it_can_export_to_csv()
    {
        // Create a mock ExportService
        $mockExportService = Mockery::mock(ExportService::class);
        $mockExportService
            ->shouldReceive('isPackageAvailable')
            ->with('csv')
            ->once()
            ->andReturn(true);
        $mockExportService
            ->shouldReceive('toCsv')
            ->once()
            ->andReturn(
                new StreamedResponse(function () {
                    echo 'CSV Content';
                }),
            );

        // Bind the mock to the container
        $this->app->instance(ExportService::class, $mockExportService);

        // Call the export method directly
        $response = $this->testComponent->export();

        // Assert that we got a response
        $this->assertNotNull($response);
    }

    #[Test]
    public function it_can_export_to_excel()
    {
        // Create a mock ExportService
        $mockExportService = Mockery::mock(ExportService::class);
        $mockExportService
            ->shouldReceive('isPackageAvailable')
            ->with('excel')
            ->once()
            ->andReturn(true);
        $mockExportService
            ->shouldReceive('toExcel')
            ->once()
            ->andReturn(
                new StreamedResponse(function () {
                    echo 'Excel Content';
                }),
            );

        // Bind the mock to the container
        $this->app->instance(ExportService::class, $mockExportService);

        // Call the export method directly
        $response = $this->testComponent->export('excel');

        // Assert that we got a response
        $this->assertNotNull($response);
    }

    #[Test]
    public function it_can_export_to_pdf()
    {
        // Create a mock ExportService
        $mockExportService = Mockery::mock(ExportService::class);
        $mockExportService
            ->shouldReceive('isPackageAvailable')
            ->with('pdf')
            ->once()
            ->andReturn(true);
        $mockExportService
            ->shouldReceive('toPdf')
            ->once()
            ->andReturn(
                new StreamedResponse(function () {
                    echo 'PDF Content';
                }),
            );

        // Bind the mock to the container
        $this->app->instance(ExportService::class, $mockExportService);

        // Call the export method directly
        $response = $this->testComponent->export('pdf');

        // Assert that we got a response
        $this->assertNotNull($response);
    }

    #[Test]
    public function it_uses_default_format_for_invalid_format()
    {
        // Create a mock ExportService
        $mockExportService = Mockery::mock(ExportService::class);
        $mockExportService
            ->shouldReceive('isPackageAvailable')
            ->with('csv') // Should default to CSV
            ->once()
            ->andReturn(true);
        $mockExportService
            ->shouldReceive('toCsv')
            ->once()
            ->andReturn(
                new StreamedResponse(function () {
                    echo 'CSV Content';
                }),
            );

        // Bind the mock to the container
        $this->app->instance(ExportService::class, $mockExportService);

        // Call the export method with an invalid format
        $response = $this->testComponent->export('invalid_format');

        // Assert that we got a response
        $this->assertNotNull($response);
    }

    #[Test]
    public function it_respects_export_disabled_config()
    {
        // Disable export in config
        Config::set('simple-datatables-and-forms.export.enable', false);

        // Call the export method
        $response = $this->testComponent->export();

        // Assert that we got no response
        $this->assertNull($response);
    }

    #[Test]
    public function export_service_can_check_package_availability()
    {
        $exportService = new ExportService;

        // CSV should always be available
        $this->assertTrue($exportService->isPackageAvailable('csv'));

        // Excel formats depend on PhpSpreadsheet
        $xlsxAvailable = $exportService->isPackageAvailable('xlsx');
        $xlsAvailable = $exportService->isPackageAvailable('xls');
        $excelAvailable = $exportService->isPackageAvailable('excel');

        // PDF depends on DomPDF
        $pdfAvailable = $exportService->isPackageAvailable('pdf');

        // These assertions will depend on whether the packages are actually installed
        // In a real environment, you might want to mock class_exists() for more predictable tests
        $this->assertIsBool($xlsxAvailable);
        $this->assertIsBool($xlsAvailable);
        $this->assertIsBool($excelAvailable);
        $this->assertIsBool($pdfAvailable);
    }

    #[Test]
    public function export_service_returns_correct_package_names()
    {
        $exportService = new ExportService;

        $this->assertEquals('phpoffice/phpspreadsheet', $exportService->getRequiredPackage('xlsx'));
        $this->assertEquals('phpoffice/phpspreadsheet', $exportService->getRequiredPackage('xls'));
        $this->assertEquals('phpoffice/phpspreadsheet', $exportService->getRequiredPackage('excel'));
        $this->assertEquals('barryvdh/laravel-dompdf', $exportService->getRequiredPackage('pdf'));
        $this->assertNull($exportService->getRequiredPackage('csv'));
        $this->assertNull($exportService->getRequiredPackage('unknown'));
    }

    #[Test]
    public function it_can_generate_custom_export_filename()
    {
        // Create a component with a custom exportFilename method
        $component = new class
        {
            use WithExport;

            public function table(Table $table): Table
            {
                return $table->query(TestModel::query())->model(TestModel::class);
            }

            // Mock these properties that would normally be set by the HasTable trait
            public string $search = '';
            public string $sortField = '';
            public string $sortDir = 'asc';
            public array $filters = [];

            // Mock the applyFiltersToQuery method that would normally be provided by WithFilters trait
            public function applyFiltersToQuery(Builder $query): void
            {
                // Do nothing in the test
            }

            // Custom exportFilename method
            public function exportFilename(string $format): string
            {
                return "custom_export_filename.{$format}";
            }
        };

        // Create a mock ExportService
        $mockExportService = Mockery::mock(ExportService::class);
        $mockExportService
            ->shouldReceive('isPackageAvailable')
            ->with('csv')
            ->once()
            ->andReturn(true);
        $mockExportService
            ->shouldReceive('toCsv')
            ->once()
            ->andReturn(
                new StreamedResponse(function () {
                    echo 'CSV Content';
                }),
            );

        // Bind the mock to the container
        $this->app->instance(ExportService::class, $mockExportService);

        // Call the export method
        $response = $component->export();

        // Assert that we got a response
        $this->assertNotNull($response);

        // Verify the custom filename method works
        $this->assertEquals('custom_export_filename.csv', $component->exportFilename('csv'));
    }
}
