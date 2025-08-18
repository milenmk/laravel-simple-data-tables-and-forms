<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Traits;

use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\WithFilters;
use PHPUnit\Framework\Attributes\Test;

class WithFiltersTest extends BaseTest
{
    private object $testClass;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an anonymous class that uses the WithFilters trait
        $this->testClass = new class
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

    #[Test]
    public function it_initializes_with_default_values()
    {
        $this->assertFalse($this->testClass->showFilters);
        $this->assertEquals([], $this->testClass->tableFilterViews);
        $this->assertEquals([], $this->testClass->filters);
        $this->assertEquals(0, $this->testClass->appliedFiltersCount);
    }

    #[Test]
    public function it_can_get_show_filters()
    {
        $this->assertFalse($this->testClass->getShowFilters());

        $this->testClass->showFilters = true;
        $this->assertTrue($this->testClass->getShowFilters());
    }

    #[Test]
    public function it_can_set_show_filters()
    {
        $this->testClass->setShowFilters(true);
        $this->assertTrue($this->testClass->showFilters);

        $this->testClass->setShowFilters(false);
        $this->assertFalse($this->testClass->showFilters);
    }

    #[Test]
    public function it_can_toggle_filters()
    {
        $this->assertFalse($this->testClass->showFilters);

        $this->testClass->toggleFilters();
        $this->assertTrue($this->testClass->showFilters);

        $this->testClass->toggleFilters();
        $this->assertFalse($this->testClass->showFilters);
    }

    #[Test]
    public function it_can_get_table_filter_views()
    {
        $views = ['filter1', 'filter2'];
        $this->testClass->tableFilterViews = $views;

        $this->assertEquals($views, $this->testClass->getTableFilterViews());
    }

    #[Test]
    public function it_can_reset_filters()
    {
        $this->testClass->filters = ['status' => 'active', 'category' => 'electronics'];
        $this->testClass->appliedFiltersCount = 2;

        $this->testClass->resetFilters();

        $this->assertEquals([], $this->testClass->filters);
        $this->assertEquals(0, $this->testClass->appliedFiltersCount);
    }

    #[Test]
    public function it_can_initialize_filters()
    {
        // This method calls prepareFilterViews which is protected
        // We'll just test that it doesn't throw an exception
        $this->testClass->initializeFilters();

        // If we get here without exception, the test passes
        $this->assertTrue(true);
    }

    #[Test]
    public function it_can_get_applied_filters_count()
    {
        $this->assertEquals(0, $this->testClass->getAppliedFiltersCount());

        // Set actual filter values to test the count
        $this->testClass->filters = ['status' => 'active', 'category' => 'electronics', 'price' => 'high'];
        $this->assertEquals(3, $this->testClass->getAppliedFiltersCount());
    }

    #[Test]
    public function it_can_update_applied_filters_count()
    {
        $this->testClass->filters = ['status' => 'active', 'category' => 'electronics', 'price' => ''];

        // Should count non-empty filters (2 in this case)
        $this->assertEquals(2, $this->testClass->getAppliedFiltersCount());
    }

    #[Test]
    public function it_handles_empty_filters_in_count()
    {
        $this->testClass->filters = ['status' => '', 'category' => null, 'price' => []];

        // Should count 0 since all filters are empty
        $this->assertEquals(0, $this->testClass->getAppliedFiltersCount());
    }

    #[Test]
    public function it_handles_mixed_filter_values_in_count()
    {
        $this->testClass->filters = [
            'status' => 'active',
            'category' => '',
            'price' => ['min' => 10, 'max' => 100],
            'tags' => null,
            'featured' => true,
        ];

        // Should count 3: status, price array, and featured boolean
        $this->assertEquals(3, $this->testClass->getAppliedFiltersCount());
    }

    #[Test]
    public function it_can_remove_filter_from_array()
    {
        $this->testClass->filters = [
            'categories' => ['electronics', 'books', 'clothing'],
            'status' => 'active',
        ];

        $this->testClass->removeFilter('categories', 'books');

        $this->assertEquals(['electronics', 'clothing'], $this->testClass->filters['categories']);
        $this->assertEquals('active', $this->testClass->filters['status']);
    }

    #[Test]
    public function it_can_remove_entire_filter_when_array_becomes_empty()
    {
        $this->testClass->filters = [
            'categories' => ['electronics'],
            'status' => 'active',
        ];

        $this->testClass->removeFilter('categories', 'electronics');

        $this->assertArrayNotHasKey('categories', $this->testClass->filters);
        $this->assertEquals('active', $this->testClass->filters['status']);
    }

    #[Test]
    public function it_can_remove_non_array_filter()
    {
        $this->testClass->filters = [
            'status' => 'active',
            'category' => 'electronics',
        ];

        $this->testClass->removeFilter('status', 'active');

        $this->assertArrayNotHasKey('status', $this->testClass->filters);
        $this->assertEquals('electronics', $this->testClass->filters['category']);
    }

    #[Test]
    public function it_handles_removing_non_existent_filter_value()
    {
        $this->testClass->filters = [
            'categories' => ['electronics', 'books'],
            'status' => 'active',
        ];

        $this->testClass->removeFilter('categories', 'non-existent');

        // Should remain unchanged
        $this->assertEquals(['electronics', 'books'], $this->testClass->filters['categories']);
    }

    #[Test]
    public function it_handles_updated_filters()
    {
        // This method should call resetPage, which we've mocked
        $this->testClass->updatedFilters();

        // If we get here without exception, the test passes
        $this->assertTrue(true);
    }

    #[Test]
    public function it_can_set_table_filter_views()
    {
        $views = ['view1', 'view2', 'view3'];
        $this->testClass->tableFilterViews = $views;

        $this->assertEquals($views, $this->testClass->getTableFilterViews());
    }

    #[Test]
    public function it_handles_boolean_filter_values_correctly()
    {
        $this->testClass->filters = [
            'is_active' => true,
            'is_featured' => false,
            'status' => '',
            'category' => null,
        ];

        // Boolean values (both true and false) should be counted as applied
        $this->assertEquals(2, $this->testClass->getAppliedFiltersCount());
    }

    #[Test]
    public function it_handles_zero_values_in_filters()
    {
        $this->testClass->filters = [
            'price' => 0,
            'quantity' => '0',
            'rating' => 0.0,
            'status' => '',
        ];

        // Zero values should not be counted as applied (they're considered empty)
        $this->assertEquals(0, $this->testClass->getAppliedFiltersCount());
    }

    #[Test]
    public function it_handles_nested_array_filters()
    {
        $this->testClass->filters = [
            'price_range' => ['min' => 10, 'max' => 100],
            'empty_range' => [],
            'categories' => ['electronics', 'books'],
            'empty_categories' => [],
        ];

        // Should count 2: price_range and categories (non-empty arrays)
        $this->assertEquals(2, $this->testClass->getAppliedFiltersCount());
    }

    #[Test]
    public function it_resets_array_keys_after_removing_filter_value()
    {
        $this->testClass->filters = [
            'categories' => ['electronics', 'books', 'clothing'],
        ];

        // Remove middle element
        $this->testClass->removeFilter('categories', 'books');

        // Array should have sequential keys
        $this->assertEquals(['electronics', 'clothing'], $this->testClass->filters['categories']);
        $this->assertEquals([0, 1], array_keys($this->testClass->filters['categories']));
    }

    #[Test]
    public function it_handles_string_numeric_filter_values()
    {
        $this->testClass->filters = [
            'price' => '100',
            'quantity' => '5',
            'empty_string' => '',
            'zero_string' => '0',
        ];

        // String numbers should be counted, but empty strings and '0' should not
        $this->assertEquals(2, $this->testClass->getAppliedFiltersCount());
    }
}
