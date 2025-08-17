<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Filters;

use Illuminate\Database\Eloquent\Builder;
use Milenmk\LaravelSimpleDatatablesAndForms\Exceptions\FilterConfigurationException;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters\SelectFilter;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class SelectFilterTest extends BaseTest
{
    protected SelectFilter $filter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filter = (new SelectFilter('category'))->label('Category');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_be_instantiated_with_name()
    {
        $this->assertSame('category', $this->filter->name);
        $this->assertSame('Category', $this->filter->label);
    }

    #[Test]
    public function it_can_be_created_using_make_method()
    {
        $filter = SelectFilter::make('status')->label('Status');

        $this->assertSame('status', $filter->name);
        $this->assertSame('Status', $filter->label);
    }

    #[Test]
    public function it_can_set_and_get_label()
    {
        $label = 'Product Category';

        $this->filter->label($label);

        $this->assertSame($label, $this->filter->label);
    }

    /**
     * @throws FilterConfigurationException
     */
    #[Test]
    public function it_can_set_and_get_options()
    {
        $options = [
            'electronics' => 'Electronics',
            'clothing' => 'Clothing',
            'books' => 'Books',
        ];

        $this->filter->options($options);

        $this->assertSame($options, $this->filter->getOptions());
    }

    #[Test]
    public function it_can_set_and_get_persist_in_session()
    {
        $this->assertFalse($this->filter->persistInSession);

        $this->filter->persistInSession();

        $this->assertTrue($this->filter->persistInSession);
    }

    #[Test]
    public function it_can_apply_filter_to_query_with_single_value()
    {
        // Create a mock query builder
        $query = Mockery::mock(Builder::class);

        // Set up expectation for a single value
        $query
            ->expects('whereIn')
            ->once()
            ->with('category', ['Electronics'])
            ->andReturnSelf();

        // Apply the filter
        $this->filter->apply($query, 'Electronics');

        // No need to assert return value as apply() returns void

        // Add a simple assertion to avoid risky test
        $this->assertTrue(true);
    }

    #[Test]
    public function it_can_apply_filter_to_query_with_multiple_values()
    {
        // Create a mock query builder
        $query = Mockery::mock(Builder::class);

        // Set up expectation for multiple values
        $query
            ->expects('whereIn')
            ->once()
            ->with('category', ['Electronics', 'Clothing'])
            ->andReturnSelf();

        // Apply the filter
        $this->filter->apply($query, ['Electronics', 'Clothing']);

        // Add a simple assertion to avoid risky test
        $this->assertTrue(true);
    }

    /**
     * @throws FilterConfigurationException
     */
    #[Test]
    public function it_can_render_view()
    {
        $this->filter->options([
            'electronics' => 'Electronics',
            'clothing' => 'Clothing',
        ]);

        $view = $this->filter->render();

        $this->assertNotNull($view);
        $this->assertStringContainsString('wire:model.live="filters.category"', $view);
        $this->assertStringContainsString('Electronics', $view);
        $this->assertStringContainsString('Clothing', $view);
    }
}
