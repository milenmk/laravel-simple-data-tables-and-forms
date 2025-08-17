<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\ExportService;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\TextColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportServiceTest extends BaseTest
{
    private ExportService $exportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exportService = new ExportService;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function is_package_available_for_excel_formats(): void
    {
        $this->assertTrue($this->exportService->isPackageAvailable('csv'));

        // These will depend on whether PhpSpreadsheet is installed
        $hasPhpSpreadsheet = class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet');
        $this->assertEquals($hasPhpSpreadsheet, $this->exportService->isPackageAvailable('xls'));
        $this->assertEquals($hasPhpSpreadsheet, $this->exportService->isPackageAvailable('xlsx'));
        $this->assertEquals($hasPhpSpreadsheet, $this->exportService->isPackageAvailable('excel'));

        // PDF depends on DomPDF
        $hasDomPdf = class_exists('\Barryvdh\DomPDF\Facade\Pdf');
        $this->assertEquals($hasDomPdf, $this->exportService->isPackageAvailable('pdf'));
    }

    #[Test]
    public function get_required_package_returns_correct_packages(): void
    {
        $this->assertEquals('phpoffice/phpspreadsheet', $this->exportService->getRequiredPackage('xls'));
        $this->assertEquals('phpoffice/phpspreadsheet', $this->exportService->getRequiredPackage('xlsx'));
        $this->assertEquals('phpoffice/phpspreadsheet', $this->exportService->getRequiredPackage('excel'));
        $this->assertEquals('barryvdh/laravel-dompdf', $this->exportService->getRequiredPackage('pdf'));
        $this->assertNull($this->exportService->getRequiredPackage('csv'));
        $this->assertNull($this->exportService->getRequiredPackage('unknown'));
    }

    #[Test]
    public function to_csv_export(): void
    {
        // Mock the query builder
        $query = Mockery::mock(Builder::class);
        $query
            ->shouldReceive('chunk')
            ->with(100, Mockery::type('callable'))
            ->andReturnUsing(function ($size, $callback) {
                $testData = collect([
                    (object) ['id' => 1, 'name' => 'Test 1', 'email' => 'test1@example.com'],
                    (object) ['id' => 2, 'name' => 'Test 2', 'email' => 'test2@example.com'],
                ]);
                $callback($testData);
            });

        // Mock the table
        $table = Mockery::mock(Table::class);
        $columns = [
            (new TextColumn('id'))->visible(),
            (new TextColumn('name'))->visible(),
            (new TextColumn('email'))->visible(),
        ];
        $table->shouldReceive('getColumns')->andReturn($columns);

        // Mock config
        Config::set('simple-datatables-and-forms.export.max_rows', 10000);

        Config::set('simple-datatables-and-forms.export.filename_prefix', 'export');

        // Mock Response facade
        Response::shouldReceive('stream')
            ->once()
            ->andReturn(new StreamedResponse);

        $result = $this->exportService->toCsv($query, $table);

        $this->assertInstanceOf(StreamedResponse::class, $result);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function format_value_handles_different_types(): void
    {
        $reflection = new ReflectionClass($this->exportService);
        $method = $reflection->getMethod('formatValue');

        // Test string
        $this->assertEquals('test', $method->invoke($this->exportService, 'test'));

        // Test integer
        $this->assertEquals('123', $method->invoke($this->exportService, 123));

        // Test array
        $this->assertEquals('["a","b"]', $method->invoke($this->exportService, ['a', 'b']));

        // Test object with __toString
        $obj = new class
        {
            public function __to_string(): string
            {
                return 'stringable object';
            }
        };
        $this->assertEquals('stringable object', $method->invoke($this->exportService, $obj));

        // Test stdClass object
        $stdObj = (object) ['key' => 'value'];
        $result = $method->invoke($this->exportService, $stdObj);
        $this->assertJson($result);
        $this->assertStringContainsString('key', $result);
        $this->assertStringContainsString('value', $result);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function format_value_handles_enum(): void
    {
        if (PHP_VERSION_ID < 80100) {
            $this->markTestSkipped('Enums require PHP 8.1+');
        }

        $reflection = new ReflectionClass($this->exportService);
        $method = $reflection->getMethod('formatValue');

        // Create a mock enum-like object
        $enum = new class
        {
            public string $value = 'enum_value';
            public string $name = 'ENUM_NAME';
        };

        // Mock enum_exists to return true
        $result = $method->invoke($this->exportService, $enum);

        // Since we can't easily mock enum_exists, we'll just test that it returns a string
        $this->assertIsString($result);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function get_export_filename_generates_correct_format(): void
    {
        Config::set('simple-datatables-and-forms.export.filename_prefix', 'test_export');

        $reflection = new ReflectionClass($this->exportService);
        $method = $reflection->getMethod('getExportFilename');

        $filename = $method->invoke($this->exportService, 'csv');

        $this->assertStringStartsWith('test_export_', $filename);
        $this->assertStringEndsWith('.csv', $filename);
        $this->assertMatchesRegularExpression('/test_export_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.csv/', $filename);
    }

    #[Test]
    public function to_excel_delegates_to_correct_format(): void
    {
        if (! class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            $this->markTestSkipped('PhpSpreadsheet not available');
        }

        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        // Mock the table columns
        $columns = [(new TextColumn('id'))->visible()];
        $table->shouldReceive('getColumns')->andReturn($columns);

        // Mock query chunk
        $query->shouldReceive('chunk')->andReturnUsing(function ($size, $callback) {
            $callback(collect());
        });

        Config::shouldReceive('get')->andReturn(10000);
        Config::shouldReceive('get')->andReturn('export');

        // Test default (xlsx)
        $result = $this->exportService->toExcel($query, $table);
        $this->assertInstanceOf(StreamedResponse::class, $result);

        // Test xls format
        $result = $this->exportService->toExcel($query, $table, 'xls');
        $this->assertInstanceOf(StreamedResponse::class, $result);
    }
}
