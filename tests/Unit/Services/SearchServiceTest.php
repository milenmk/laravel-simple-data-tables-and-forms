<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\SearchService;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\Column;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class SearchServiceTest extends BaseTest
{
    private SearchService $searchService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->searchService = new SearchService;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_query_unchanged_for_empty_search()
    {
        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        $result = $this->searchService->applySearch($query, '', $table);

        $this->assertSame($query, $result);
    }

    #[Test]
    public function it_returns_query_unchanged_for_search_below_minimum_characters()
    {
        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        Config::set('simple-datatables-and-forms.search.min_characters', 3);

        $result = $this->searchService->applySearch($query, 'ab', $table);

        $this->assertSame($query, $result);
    }

    #[Test]
    public function it_applies_like_search_by_default()
    {
        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        Config::set('simple-datatables-and-forms.search.min_characters', 2);
        Config::set('simple-datatables-and-forms.search.default_mode', 'like');

        // Create mock columns
        $column1 = Mockery::mock(Column::class);
        $column1->searchable = true;
        $column1->key = 'name';

        $column2 = Mockery::mock(Column::class);
        $column2->searchable = false;
        $column2->key = 'id';

        $table->shouldReceive('getColumns')->andReturn([$column1, $column2]);

        // Mock the where method chain
        $whereQuery = Mockery::mock(Builder::class);
        $query
            ->shouldReceive('where')
            ->with(Mockery::type('Closure'))
            ->andReturn($whereQuery);

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($whereQuery, $result);
    }

    #[Test]
    public function it_applies_exact_search_when_configured()
    {
        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        Config::set('simple-datatables-and-forms.search.min_characters', 2);
        Config::set('simple-datatables-and-forms.search.default_mode', 'exact');

        // Create mock columns
        $column1 = Mockery::mock(Column::class);
        $column1->searchable = true;
        $column1->key = 'name';

        $table->shouldReceive('getColumns')->andReturn([$column1]);

        // Mock the where method chain
        $whereQuery = Mockery::mock(Builder::class);
        $query
            ->shouldReceive('where')
            ->with(Mockery::type('Closure'))
            ->andReturn($whereQuery);

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($whereQuery, $result);
    }

    #[Test]
    public function it_returns_query_unchanged_when_no_searchable_columns()
    {
        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        Config::set('simple-datatables-and-forms.search.min_characters', 2);
        Config::set('simple-datatables-and-forms.search.default_mode', 'like');

        // Create mock columns with no searchable columns
        $column1 = Mockery::mock(Column::class);
        $column1->searchable = false;
        $column1->key = 'id';

        $table->shouldReceive('getColumns')->andReturn([$column1]);

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($query, $result);
    }

    #[Test]
    public function it_handles_fulltext_search_fallback()
    {
        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        Config::set('simple-datatables-and-forms.search.min_characters', 2);
        Config::set('simple-datatables-and-forms.search.default_mode', 'fulltext');
        Config::set('simple-datatables-and-forms.search.enable_fulltext', true);

        // Create mock columns
        $column1 = Mockery::mock(Column::class);
        $column1->searchable = true;
        $column1->key = 'name';

        $table->shouldReceive('getColumns')->andReturn([$column1]);

        // Mock connection to return non-MySQL driver (to trigger fallback)
        $connection = Mockery::mock();
        $connection->shouldReceive('getDriverName')->andReturn('sqlite');
        $query->shouldReceive('getConnection')->andReturn($connection);

        // Mock the where method chain for fallback to like search
        $whereQuery = Mockery::mock(Builder::class);
        $query
            ->shouldReceive('where')
            ->with(Mockery::type('Closure'))
            ->andReturn($whereQuery);

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($whereQuery, $result);
    }

    #[Test]
    public function it_handles_fulltext_search_disabled()
    {
        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        Config::set('simple-datatables-and-forms.search.min_characters', 2);
        Config::set('simple-datatables-and-forms.search.default_mode', 'fulltext');
        Config::set('simple-datatables-and-forms.search.enable_fulltext', false);

        // Create mock columns
        $column1 = Mockery::mock(Column::class);
        $column1->searchable = true;
        $column1->key = 'name';

        $table->shouldReceive('getColumns')->andReturn([$column1]);

        // Mock the where method chain for fallback to like search
        $whereQuery = Mockery::mock(Builder::class);
        $query
            ->shouldReceive('where')
            ->with(Mockery::type('Closure'))
            ->andReturn($whereQuery);

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($whereQuery, $result);
    }

    #[Test]
    public function it_handles_unknown_search_mode()
    {
        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        Config::set('simple-datatables-and-forms.search.min_characters', 2);
        Config::set('simple-datatables-and-forms.search.default_mode', 'unknown_mode');

        // Create mock columns
        $column1 = Mockery::mock(Column::class);
        $column1->searchable = true;
        $column1->key = 'name';

        $table->shouldReceive('getColumns')->andReturn([$column1]);

        // Mock the where method chain for fallback to like search
        $whereQuery = Mockery::mock(Builder::class);
        $query
            ->shouldReceive('where')
            ->with(Mockery::type('Closure'))
            ->andReturn($whereQuery);

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($whereQuery, $result);
    }
}
