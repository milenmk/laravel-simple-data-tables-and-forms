<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Filters;

use Illuminate\Database\Eloquent\Builder;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters\TernaryFilter;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Mockery;

class TernaryFilterTest extends BaseTest
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function ternary_filter_creation(): void
    {
        $filter = new TernaryFilter('active');

        $this->assertEquals('active', $filter->name);
        $this->assertTrue($filter->toggle);
    }

    /**
     * @test
     */
    public function ternary_filter_toggle_configuration(): void
    {
        $filter = new TernaryFilter('published');

        // Test enabling toggle
        $result = $filter->toggle();
        $this->assertTrue($filter->toggle);
        $this->assertSame($filter, $result); // Test fluent interface

        // Test disabling toggle
        $filter->toggle(false);
        $this->assertFalse($filter->toggle);

        // Test default parameter (true)
        $filter->toggle();
        $this->assertTrue($filter->toggle);
    }

    /**
     * @test
     */
    public function ternary_filter_apply_with_true_boolean(): void
    {
        $filter = new TernaryFilter('active');
        $query = Mockery::mock(Builder::class);

        $query
            ->shouldReceive('where')
            ->with('active', true)
            ->once()
            ->andReturnSelf();

        $result = $filter->apply($query, true);

        // Verify the method completes without error
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function ternary_filter_apply_with_true_string(): void
    {
        $filter = new TernaryFilter('active');
        $query = Mockery::mock(Builder::class);

        $query
            ->shouldReceive('where')
            ->with('active', true)
            ->once()
            ->andReturnSelf();

        $result = $filter->apply($query, 'true');

        // Verify the method completes without error
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function ternary_filter_apply_with_false_boolean(): void
    {
        $filter = new TernaryFilter('active');
        $query = Mockery::mock(Builder::class);

        $query
            ->shouldReceive('where')
            ->with('active', false)
            ->once()
            ->andReturnSelf();

        $result = $filter->apply($query, false);

        // Verify the method completes without error
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function ternary_filter_apply_with_false_string(): void
    {
        $filter = new TernaryFilter('active');
        $query = Mockery::mock(Builder::class);

        $query
            ->shouldReceive('where')
            ->with('active', false)
            ->once()
            ->andReturnSelf();

        $result = $filter->apply($query, 'false');

        // Verify the method completes without error
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function ternary_filter_apply_with_null_value(): void
    {
        $filter = new TernaryFilter('active');
        $query = Mockery::mock(Builder::class);

        // Should not call where when value is null
        $query->shouldNotReceive('where');

        $result = $filter->apply($query, null);

        // Verify the method completes without error
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function ternary_filter_apply_with_custom_closure(): void
    {
        $customQuery = function ($query, $value) {
            $query->where('status', $value ? 'enabled' : 'disabled');
        };

        $filter = new TernaryFilter('status');
        $filter->query($customQuery);

        $query = Mockery::mock(Builder::class);
        $query
            ->shouldReceive('where')
            ->with('status', 'enabled')
            ->once()
            ->andReturnSelf();

        $result = $filter->apply($query, true);

        // Verify the method completes without error
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function ternary_filter_render(): void
    {
        $filter = new TernaryFilter('active');
        $filter->label('Active Status');

        $view = $filter->render();

        $this->assertEquals('laravel-simple-datatables-and-forms::components.table.filters.ternary', $view->getName());
        $this->assertEquals($filter, $view->getData()['filter']);
    }

    /**
     * @test
     */
    public function ternary_filter_fluent_interface(): void
    {
        $filter = new TernaryFilter('published');

        $result = $filter->label('Publication Status')->toggle(false);

        $this->assertEquals('Publication Status', $filter->label);
        $this->assertFalse($filter->toggle);
        $this->assertSame($filter, $result);
    }
}
