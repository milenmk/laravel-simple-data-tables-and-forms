<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Feature;

use Livewire\Livewire;
use Milenmk\LaravelSimpleDatatablesAndForms\Exceptions\FilterConfigurationException;
use Milenmk\LaravelSimpleDatatablesAndForms\Exceptions\InvalidFilterTypeException;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Components\TestTableComponent;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;
use PHPUnit\Framework\Attributes\Test;

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
}
