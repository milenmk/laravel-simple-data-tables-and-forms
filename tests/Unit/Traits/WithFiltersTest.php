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
}
