<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Traits;

use Illuminate\Database\Eloquent\Builder;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters\SelectFilter;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\WithFilters;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use ReflectionException;

class WithFiltersTest extends BaseTest
{
    private object $component;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test component that uses the WithFilters trait
        $this->component = new class
        {
            use WithFilters;

            public function table(Table $table): Table
            {
                return $table;
            }

            public function resetPage(): void
            {
                // Mock resetPage method
            }
        };
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function with_filters_initialization(): void
    {
        $this->assertFalse($this->component->showFilters);
        $this->assertEquals([], $this->component->tableFilterViews);
        $this->assertEquals([], $this->component->filters);
        $this->assertEquals(0, $this->component->appliedFiltersCount);
    }

    #[Test]
    public function get_show_filters(): void
    {
        $this->assertFalse($this->component->getShowFilters());

        $this->component->showFilters = true;
        $this->assertTrue($this->component->getShowFilters());
    }

    #[Test]
    public function set_show_filters(): void
    {
        $this->component->setShowFilters(true);
        $this->assertTrue($this->component->showFilters);

        $this->component->setShowFilters(false);
        $this->assertFalse($this->component->showFilters);
    }

    #[Test]
    public function toggle_filters(): void
    {
        $this->assertFalse($this->component->showFilters);

        $this->component->toggleFilters();
        $this->assertTrue($this->component->showFilters);

        $this->component->toggleFilters();
        $this->assertFalse($this->component->showFilters);
    }

    #[Test]
    public function get_table_filter_views(): void
    {
        $views = ['filter1' => 'view1'];
        $this->component->tableFilterViews = $views;

        $this->assertEquals($views, $this->component->getTableFilterViews());
    }

    #[Test]
    public function get_applied_filters_count(): void
    {
        $this->assertEquals(0, $this->component->getAppliedFiltersCount());

        $this->component->appliedFiltersCount = 3;
        $this->assertEquals(3, $this->component->getAppliedFiltersCount());
    }

    #[Test]
    public function remove_filter_with_array_value(): void
    {
        $this->component->filters = [
            'status' => ['active', 'pending', 'inactive'],
        ];

        $this->component->removeFilter('status', 'pending');

        $this->assertEquals(['active', 'inactive'], $this->component->filters['status']);
    }

    #[Test]
    public function remove_filter_removes_empty_array(): void
    {
        $this->component->filters = [
            'status' => ['active'],
        ];

        $this->component->removeFilter('status', 'active');

        $this->assertArrayNotHasKey('status', $this->component->filters);
    }

    #[Test]
    public function remove_filter_with_non_array_value(): void
    {
        $this->component->filters = [
            'category' => 'electronics',
            'status' => 'active',
        ];

        $this->component->removeFilter('category', 'electronics');

        $this->assertArrayNotHasKey('category', $this->component->filters);
        $this->assertArrayHasKey('status', $this->component->filters);
    }

    #[Test]
    public function remove_filter_with_non_existent_value(): void
    {
        $this->component->filters = [
            'status' => ['active', 'pending'],
        ];

        $this->component->removeFilter('status', 'inactive');

        // Should remain unchanged
        $this->assertEquals(['active', 'pending'], $this->component->filters['status']);
    }

    #[Test]
    public function updated_filters_resets_page(): void
    {
        // This test verifies that the updatedFilters method exists and can be called
        $this->component->updatedFilters();

        // Since resetPage is mocked, we just verify the method doesn't throw an error
        $this->assertTrue(true);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function apply_filters_to_query(): void
    {
        // Create a mock filter
        $filter = Mockery::mock(SelectFilter::class);
        $filter->name = 'status';
        $filter->persistInSession = false;
        $filter->shouldReceive('apply')->once();

        // Create a mock table with filters
        $table = Mockery::mock(Table::class);
        $table->shouldReceive('getFilters')->andReturn([$filter]);

        // Override the table method to return our mock
        $component = new class
        {
            use WithFilters;

            public $mockTable;

            public function table(Table $table): Table
            {
                return $this->mockTable ?? $table;
            }

            public function resetPage(): void
            {
                // Mock resetPage method
            }
        };

        $component->mockTable = $table;
        $component->filters = ['status' => 'active'];

        $query = Mockery::mock(Builder::class);

        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('applyFiltersToQuery');

        $method->invoke($component, $query);

        $this->assertEquals(1, $component->appliedFiltersCount);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function apply_filters_to_query_with_empty_filter(): void
    {
        // Create a mock filter
        $filter = Mockery::mock(SelectFilter::class);
        $filter->name = 'status';
        $filter->persistInSession = false;
        $filter->shouldNotReceive('apply'); // Should not be called for empty filter

        // Create a mock table with filters
        $table = Mockery::mock(Table::class);
        $table->shouldReceive('getFilters')->andReturn([$filter]);

        // Override the table method to return our mock
        $component = new class
        {
            use WithFilters;

            public $mockTable;

            public function table(Table $table): Table
            {
                return $this->mockTable ?? $table;
            }

            public function resetPage(): void
            {
                // Mock resetPage method
            }
        };

        $component->mockTable = $table;
        $component->filters = ['status' => ''];

        $query = Mockery::mock(Builder::class);

        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('applyFiltersToQuery');

        $method->invoke($component, $query);

        $this->assertEquals(0, $component->appliedFiltersCount);
    }
}
