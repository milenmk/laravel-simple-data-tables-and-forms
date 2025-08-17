<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Filters;

use Illuminate\Database\Eloquent\Builder;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters\Filter;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class FilterTest extends BaseTest
{
    private Filter $filter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filter = Filter::make('active');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_be_created()
    {
        $filter = Filter::make('status');

        $this->assertEquals('status', $filter->name);
        $this->assertEquals('status', $filter->label);
        $this->assertFalse($filter->toggle);
    }

    #[Test]
    public function it_can_set_toggle()
    {
        $this->filter->toggle();

        $this->assertTrue($this->filter->toggle);

        $this->filter->toggle(false);

        $this->assertFalse($this->filter->toggle);
    }

    #[Test]
    public function it_applies_filter_with_custom_query_closure()
    {
        $query = Mockery::mock(Builder::class);
        $customQuery = function ($q, $value) {
            $q->where('custom_field', $value);
        };

        $this->filter->query($customQuery);

        $query
            ->expects('where')
            ->once()
            ->with('custom_field', 'test_value')
            ->andReturnSelf();

        $this->filter->apply($query, 'test_value');

        // Add assertion to avoid risky test
        $this->assertTrue(true);
    }

    #[Test]
    public function it_applies_filter_with_true_value()
    {
        $query = Mockery::mock(Builder::class);

        $query
            ->expects('where')
            ->once()
            ->with('active', true)
            ->andReturnSelf();

        $this->filter->apply($query, true);

        // Add assertion to avoid risky test
        $this->assertTrue(true);
    }

    #[Test]
    public function it_applies_filter_with_string_true_value()
    {
        $query = Mockery::mock(Builder::class);

        $query
            ->expects('where')
            ->once()
            ->with('active', true)
            ->andReturnSelf();

        $this->filter->apply($query, 'true');

        // Add assertion to avoid risky test
        $this->assertTrue(true);
    }

    #[Test]
    public function it_applies_filter_with_false_value()
    {
        $query = Mockery::mock(Builder::class);

        $query
            ->expects('where')
            ->once()
            ->with('active', false)
            ->andReturnSelf();

        $this->filter->apply($query, false);

        // Add assertion to avoid risky test
        $this->assertTrue(true);
    }

    #[Test]
    public function it_applies_filter_with_string_false_value()
    {
        $query = Mockery::mock(Builder::class);

        $query
            ->expects('where')
            ->once()
            ->with('active', false)
            ->andReturnSelf();

        $this->filter->apply($query, 'false');

        // Add assertion to avoid risky test
        $this->assertTrue(true);
    }

    #[Test]
    public function it_does_not_apply_filter_with_null_value()
    {
        $query = Mockery::mock(Builder::class);

        $query->shouldNotReceive('where');

        $this->filter->apply($query, null);

        // Add assertion to avoid risky test
        $this->assertTrue(true);
    }

    #[Test]
    public function it_does_not_apply_filter_with_other_values_when_no_custom_query()
    {
        $query = Mockery::mock(Builder::class);

        $query->shouldNotReceive('where');

        $this->filter->apply($query, 'other_value');
        $this->filter->apply($query, 123);
        $this->filter->apply($query, []);

        // Add assertion to avoid risky test
        $this->assertTrue(true);
    }

    #[Test]
    public function it_can_render()
    {
        $view = $this->filter->render();

        $this->assertNotNull($view);

        $html = (string) $view; // render the view to HTML string

        $this->assertStringContainsString('wire:model.live="filters.active"', $html);
    }

    #[Test]
    public function it_renders_with_custom_label()
    {
        $this->filter->label('Is Active');

        $view = $this->filter->render();

        $html = (string) $view; // render the view to HTML string

        $this->assertStringContainsString('Is Active', $html);
    }

    #[Test]
    public function it_prioritizes_custom_query_over_default_behavior()
    {
        $query = Mockery::mock(Builder::class);
        $customQuery = function ($q, $value) {
            $q->where('custom_logic', '!=', $value);
        };

        $this->filter->query($customQuery);

        // Should use custom query, not default boolean logic
        $query
            ->expects('where')
            ->once()
            ->with('custom_logic', '!=', 'true')
            ->andReturnSelf();

        $this->filter->apply($query, 'true');

        // Add assertion to avoid risky test
        $this->assertTrue(true);
    }
}
