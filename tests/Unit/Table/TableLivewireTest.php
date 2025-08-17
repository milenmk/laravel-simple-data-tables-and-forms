<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table;

use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;
use PHPUnit\Framework\Attributes\Test;

class TableLivewireTest extends BaseTest
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

        // Register a directive to mock the Livewire variable
        Blade::directive('livewire', function () {
            return "<?php echo 'Mocked Livewire Component'; ?>";
        });

        // Mock the $__livewire variable that's used in the table component
        app()->singleton('__livewire', function () {
            return (object) [
                'id' => 'mock-id',
                'fingerprint' => ['id' => 'mock-id', 'name' => 'mock-name', 'path' => 'mock-path'],
                'effects' => ['listeners' => []],
                'properties' => [],
            ];
        });
    }

    #[Test]
    public function it_can_mount_livewire_component_with_table()
    {
        // This test verifies that the Livewire component with HasTable trait can be mounted
        // We're not testing the actual rendering of the table here, just that the component initializes properly
        Livewire::test(MockTableComponent::class)->assertSuccessful();
    }

    #[Test]
    public function it_can_prepare_table_data_in_livewire_component()
    {
        // Instead of relying on Livewire's instance() method which might return null in Livewire 3,
        // we'll create a direct instance of the component for testing
        $mockComponent = new MockTableComponent;

        // Verify that the table method returns a Table instance with expected configuration
        $table = $mockComponent->table(new Table);
        $this->assertEquals('Test Table', $table->heading);
        $this->assertCount(2, $table->getColumns());

        // Also test that the Livewire component can be mounted successfully
        Livewire::test(MockTableComponent::class)->assertSuccessful();
    }
}
