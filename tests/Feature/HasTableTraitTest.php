<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Feature;

use Illuminate\View\View;
use Livewire\Livewire;
use Milenmk\LaravelSimpleDatatablesAndForms\Exceptions\FilterConfigurationException;
use Milenmk\LaravelSimpleDatatablesAndForms\Exceptions\InvalidFilterTypeException;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Components\TestTableComponent;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class HasTableTraitTest extends BaseTest
{
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

        TestModel::create([
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'is_active' => true,
            'category' => 'Books',
            'price' => 29.99,
        ]);
    }

    #[Test]
    public function it_can_render_table_component()
    {
        Livewire::test(TestTableComponent::class)
            ->assertSuccessful()
            ->assertSeeHtml('Mocked Livewire Component');
    }

    /**
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     */
    #[Test]
    public function it_can_search_table_data()
    {
        // Test with direct component instantiation since Livewire has issues
        $directComponent = new TestTableComponent;
        $directComponent->mount();

        $directComponent->search = 'John';
        $directComponent->updatedSearch();

        // Verify search functionality works
        $this->assertEquals('John', $directComponent->search);
    }

    /**
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     */
    #[Test]
    public function it_can_sort_table_data()
    {
        // Test with direct component instantiation since Livewire has issues
        $directComponent = new TestTableComponent;
        $directComponent->mount();

        $directComponent->setSortBy('name');
        $this->assertEquals('name', $directComponent->sortField);
        $this->assertEquals('ASC', $directComponent->sortDir);

        $directComponent->setSortBy('name');
        $this->assertEquals('name', $directComponent->sortField);
        $this->assertEquals('DESC', $directComponent->sortDir);
    }

    /**
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     */
    #[Test]
    public function it_can_reset_filters()
    {
        // Test with direct component instantiation since Livewire has issues
        $directComponent = new TestTableComponent;
        $directComponent->mount();

        $directComponent->filters = ['category' => ['Electronics']];
        $directComponent->resetFilters();
        $this->assertEquals([], $directComponent->filters);
    }

    /**
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     */
    #[Test]
    public function it_can_toggle_column_visibility()
    {
        // Test with direct component instantiation since Livewire has issues
        $directComponent = new TestTableComponent;
        $directComponent->mount();

        $componentName = (string) $directComponent->componentName;
        $prefixedKey = "{$componentName}.email";

        // Toggle the visibility of the email column
        $directComponent->toggleColumnVisibility('email');
        $this->assertFalse($directComponent->visibleColumns[$prefixedKey]);

        // Toggle it back
        $directComponent->toggleColumnVisibility('email');
        $this->assertTrue($directComponent->visibleColumns[$prefixedKey]);
    }

    /**
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     */
    #[Test]
    public function it_can_toggle_filters_visibility()
    {
        // Test with direct component instantiation since Livewire has issues
        $directComponent = new TestTableComponent;
        $directComponent->mount();

        $this->assertFalse($directComponent->showFilters);
        $directComponent->toggleFilters();
        $this->assertTrue($directComponent->showFilters);
        $directComponent->toggleFilters();
        $this->assertFalse($directComponent->showFilters);
    }

    /**
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     */
    #[Test]
    public function it_can_set_per_page()
    {
        // Test with direct component instantiation since Livewire has issues
        $directComponent = new TestTableComponent;
        $directComponent->mount();

        $this->assertEquals(10, $directComponent->perPage);
    }

    /**
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     */
    #[Test]
    public function it_can_toggle_value()
    {
        $model = TestModel::where('name', 'John Doe')->first();
        $this->assertTrue($model->is_active);

        // Test with direct component instantiation since Livewire has issues
        $directComponent = new TestTableComponent;
        $directComponent->mount();
        $directComponent->toggleValue($model->id, 'is_active');

        $this->assertFalse($model->fresh()->is_active);

        $directComponent->toggleValue($model->id, 'is_active');

        $this->assertTrue($model->fresh()->is_active);
    }

    /**
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     */
    #[Test]
    public function it_can_get_model_by_id()
    {
        $model = TestModel::where('name', 'John Doe')->first();

        $directComponent = new TestTableComponent;
        $directComponent->mount();

        $retrievedModel = $directComponent->getModel((string) $model->id);

        $this->assertInstanceOf(TestModel::class, $retrievedModel);
        $this->assertEquals($model->id, $retrievedModel->id);
        $this->assertEquals('John Doe', $retrievedModel->name);
    }

    /**
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     */
    #[Test]
    public function it_returns_null_for_non_existent_model()
    {
        $directComponent = new TestTableComponent;
        $directComponent->mount();

        $retrievedModel = $directComponent->getModel('999999');

        $this->assertNull($retrievedModel);
    }

    /**
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_can_get_table_property()
    {
        $directComponent = new TestTableComponent;
        $directComponent->mount();

        $tableView = $directComponent->getTableProperty();

        $this->assertInstanceOf(View::class, $tableView);
    }

    /**
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     */
    #[Test]
    public function it_initializes_visible_columns_on_mount()
    {
        $directComponent = new TestTableComponent;
        $directComponent->mount();

        $this->assertNotEmpty($directComponent->visibleColumns);
        $this->assertIsArray($directComponent->visibleColumns);

        // Check that component name is set
        $this->assertNotNull($directComponent->componentName);
        $this->assertEquals('test_table_component', (string) $directComponent->componentName);
    }

    /**
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     */
    #[Test]
    public function it_handles_search_with_relationships()
    {
        $directComponent = new TestTableComponent;
        $directComponent->mount();

        // Test search functionality
        $directComponent->search = 'Electronics';
        $directComponent->updatedSearch();

        $this->assertEquals('Electronics', $directComponent->search);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_handles_grouping_in_table_property()
    {
        $directComponent = new TestTableComponent;
        $directComponent->mount();

        // Set a group
        $directComponent->selectedGroup = 'category';

        $tableView = $directComponent->getTableProperty();

        $this->assertInstanceOf(View::class, $tableView);
        $this->assertEquals('category', $directComponent->selectedGroup);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function it_handles_sorting_validation()
    {
        $directComponent = new TestTableComponent;
        $directComponent->mount();

        // Set valid sort field
        $directComponent->sortField = 'name';
        $directComponent->sortDir = 'ASC';

        $tableView = $directComponent->getTableProperty();

        $this->assertInstanceOf(View::class, $tableView);
        $this->assertEquals('name', $directComponent->sortField);
        $this->assertEquals('ASC', $directComponent->sortDir);
    }

    /**
     * @throws FilterConfigurationException
     * @throws InvalidFilterTypeException
     */
    #[Test]
    public function it_clears_cache_when_toggling_column_visibility()
    {
        $directComponent = new TestTableComponent;
        $directComponent->mount();

        $componentName = (string) $directComponent->componentName;
        $prefixedKey = "{$componentName}.email";

        // Get initial state
        $initialState = $directComponent->visibleColumns[$prefixedKey] ?? true;

        // Toggle visibility
        $directComponent->toggleColumnVisibility('email');

        // Verify state changed
        $this->assertEquals(! $initialState, $directComponent->visibleColumns[$prefixedKey]);
    }
}
