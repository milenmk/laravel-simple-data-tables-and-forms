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
    public function it_can_check_if_package_is_available_for_csv()
    {
        $this->assertTrue($this->exportService->isPackageAvailable('csv'));
    }

    #[Test]
    public function it_can_check_if_package_is_available_for_excel()
    {
        // This will depend on whether PhpSpreadsheet is installed
        $result = $this->exportService->isPackageAvailable('xlsx');
        $this->assertIsBool($result);
    }

    #[Test]
    public function it_can_check_if_package_is_available_for_xls()
    {
        // This will depend on whether PhpSpreadsheet is installed
        $result = $this->exportService->isPackageAvailable('xls');
        $this->assertIsBool($result);
    }

    #[Test]
    public function it_can_check_if_package_is_available_for_excel_alias()
    {
        // This will depend on whether PhpSpreadsheet is installed
        $result = $this->exportService->isPackageAvailable('excel');
        $this->assertIsBool($result);
    }

    #[Test]
    public function it_can_check_if_package_is_available_for_pdf()
    {
        // This will depend on whether DomPDF is installed
        $result = $this->exportService->isPackageAvailable('pdf');
        $this->assertIsBool($result);
    }

    #[Test]
    public function it_returns_true_for_unknown_formats()
    {
        $this->assertTrue($this->exportService->isPackageAvailable('unknown'));
    }

    #[Test]
    public function it_can_get_required_package_for_excel()
    {
        $this->assertEquals('phpoffice/phpspreadsheet', $this->exportService->getRequiredPackage('xlsx'));
        $this->assertEquals('phpoffice/phpspreadsheet', $this->exportService->getRequiredPackage('xls'));
        $this->assertEquals('phpoffice/phpspreadsheet', $this->exportService->getRequiredPackage('excel'));
    }

    #[Test]
    public function it_can_get_required_package_for_pdf()
    {
        $this->assertEquals('barryvdh/laravel-dompdf', $this->exportService->getRequiredPackage('pdf'));
    }

    #[Test]
    public function it_returns_null_for_csv_package()
    {
        $this->assertNull($this->exportService->getRequiredPackage('csv'));
    }

    #[Test]
    public function it_returns_null_for_unknown_format_package()
    {
        $this->assertNull($this->exportService->getRequiredPackage('unknown'));
    }
}
