<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Services;

use Milenmk\LaravelSimpleDatatablesAndForms\Services\ExportService;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;

class ExportServiceTest extends BaseTest
{
    private ExportService $exportService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exportService = new ExportService;
    }

    #[Test]
    public function it_checks_if_package_is_available_for_csv()
    {
        // CSV doesn't require external packages
        $this->assertTrue($this->exportService->isPackageAvailable('csv'));
    }

    #[Test]
    public function it_checks_if_package_is_available_for_excel_formats()
    {
        // These will return true if PhpSpreadsheet is installed, false otherwise
        $xlsAvailable = $this->exportService->isPackageAvailable('xls');
        $xlsxAvailable = $this->exportService->isPackageAvailable('xlsx');
        $excelAvailable = $this->exportService->isPackageAvailable('excel');

        // All should return the same result since they check for the same class
        $this->assertEquals($xlsAvailable, $xlsxAvailable);
        $this->assertEquals($xlsxAvailable, $excelAvailable);

        // The result depends on whether PhpSpreadsheet is installed
        $this->assertIsBool($xlsAvailable);
    }

    #[Test]
    public function it_checks_if_package_is_available_for_pdf()
    {
        // This will return true if DomPDF is installed, false otherwise
        $pdfAvailable = $this->exportService->isPackageAvailable('pdf');

        $this->assertIsBool($pdfAvailable);
    }

    #[Test]
    public function it_checks_if_package_is_available_for_unknown_format()
    {
        // Unknown formats should return true (default case)
        $this->assertTrue($this->exportService->isPackageAvailable('unknown'));
        $this->assertTrue($this->exportService->isPackageAvailable('txt'));
    }

    #[Test]
    public function it_returns_required_package_for_excel_formats()
    {
        $this->assertEquals('phpoffice/phpspreadsheet', $this->exportService->getRequiredPackage('xls'));
        $this->assertEquals('phpoffice/phpspreadsheet', $this->exportService->getRequiredPackage('xlsx'));
        $this->assertEquals('phpoffice/phpspreadsheet', $this->exportService->getRequiredPackage('excel'));
    }

    #[Test]
    public function it_returns_required_package_for_pdf()
    {
        $this->assertEquals('barryvdh/laravel-dompdf', $this->exportService->getRequiredPackage('pdf'));
    }

    #[Test]
    public function it_returns_null_for_formats_without_required_packages()
    {
        $this->assertNull($this->exportService->getRequiredPackage('csv'));
        $this->assertNull($this->exportService->getRequiredPackage('txt'));
        $this->assertNull($this->exportService->getRequiredPackage('unknown'));
    }

    #[Test]
    public function it_handles_case_sensitivity_in_format_checking()
    {
        // Test that format matching is case-sensitive (as expected from match statement)
        $this->assertEquals('phpoffice/phpspreadsheet', $this->exportService->getRequiredPackage('xlsx'));
        $this->assertNull($this->exportService->getRequiredPackage('XLSX')); // Different case
    }

    #[Test]
    public function it_provides_consistent_results_for_same_format()
    {
        // Test that multiple calls return consistent results
        $format = 'xlsx';

        $available1 = $this->exportService->isPackageAvailable($format);
        $available2 = $this->exportService->isPackageAvailable($format);

        $package1 = $this->exportService->getRequiredPackage($format);
        $package2 = $this->exportService->getRequiredPackage($format);

        $this->assertEquals($available1, $available2);
        $this->assertEquals($package1, $package2);
    }

    #[Test]
    public function it_handles_empty_format_string()
    {
        // Empty string should fall to default case
        $this->assertTrue($this->exportService->isPackageAvailable(''));
        $this->assertNull($this->exportService->getRequiredPackage(''));
    }
}
