<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\View\ComponentAttributeBag;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\TextColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

class TableRenderTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        TestModel::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_active' => true,
        ]);

        // Mock the Livewire variable
        View::share(
            '__livewire',
            (object) [
                'id' => 'mock-id',
                'fingerprint' => ['id' => 'mock-id', 'name' => 'mock-name', 'path' => 'mock-path'],
                'effects' => ['listeners' => []],
                'properties' => [],
            ],
        );

        // Mock the wire directive
        Blade::directive('wire', function ($expression) {
            return "<?php echo 'wire:' . {$expression}; ?>";
        });

        // Mock the entangle function
        Blade::directive('entangle', function () {
            return "<?php echo ''; ?>";
        });
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function it_can_render_table_with_mocked_livewire()
    {
        // Create and configure the table
        $table = new Table;
        $query = TestModel::query()->paginate(10);
        $columns = [TextColumn::make('name')->label('Name'), TextColumn::make('email')->label('Email')];

        $table
            ->query($query)
            ->schema($columns)
            ->heading('Test Table');

        // Use the view file from the test directory
        $view = View::file(__DIR__ . '/../../views/test/test-table.blade.php', [
            'data' => $query,
            'columns' => $columns,
            'heading' => 'Test Table',
            'striped' => false,
            'hover' => true,
            'borders' => 'all',
            'size' => 'md',
            'theme' => 'light',
            'groups' => null,
            'selectedGroup' => null,
            'collapsedGroups' => [],
            'showFilters' => false,
            'tableFilters' => [],
            'filters' => [],
            'csrfField' => '',
            'exportEnabled' => true,
            'exportFormats' => ['csv'],
            'attributes' => new ComponentAttributeBag,
        ]);

        $renderedView = $view->render();

        // Assert that the view contains expected content
        $this->assertStringContainsString('Test Table', $renderedView);
        $this->assertStringContainsString('Name', $renderedView);
        $this->assertStringContainsString('Email', $renderedView);
        $this->assertStringContainsString('Test User', $renderedView);
        $this->assertStringContainsString('test@example.com', $renderedView);
    }
}
