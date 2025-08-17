<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Services;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Config;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\SearchService;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\TextColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use ReflectionException;

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
    public function apply_search_returns_query_when_search_is_empty(): void
    {
        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        $result = $this->searchService->applySearch($query, '', $table);

        $this->assertSame($query, $result);
    }

    #[Test]
    public function apply_search_with_like_mode(): void
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        Config::set('simple-datatables-and-forms.search.default_mode', 'like');

        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        // Create searchable columns
        $column1 = new TextColumn('name');
        $column1->searchable();
        $column2 = new TextColumn('email');
        $column2->searchable();

        $table->shouldReceive('getColumns')->andReturn([$column1, $column2]);

        $query
            ->shouldReceive('where')
            ->once()
            ->with(Mockery::type('Closure'))
            ->andReturnSelf();

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($query, $result);
    }

    #[Test]
    public function apply_search_with_exact_mode(): void
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        Config::set('simple-datatables-and-forms.search.default_mode', 'like');

        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        // Create searchable columns
        $column1 = new TextColumn('name');
        $column1->searchable();

        $table->shouldReceive('getColumns')->andReturn([$column1]);

        $query
            ->shouldReceive('where')
            ->once()
            ->with(Mockery::type('Closure'))
            ->andReturnSelf();

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($query, $result);
    }

    #[Test]
    public function apply_search_with_fulltext_mode_fallback_to_like(): void
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        Config::set('simple-datatables-and-forms.search.default_mode', 'like');

        Config::set('simple-datatables-and-forms.search.enable_fulltext', false);

        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        // Create searchable columns
        $column1 = new TextColumn('name');
        $column1->searchable();

        $table->shouldReceive('getColumns')->andReturn([$column1]);

        // Mock connection to return non-MySQL driver
        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('getDriverName')->andReturn('sqlite');
        $query->shouldReceive('getConnection')->andReturn($connection);

        $queryBuilder = Mockery::mock(QueryBuilder::class);
        $queryBuilder->from = 'users';
        $query->shouldReceive('getQuery')->andReturn($queryBuilder);

        $query
            ->shouldReceive('where')
            ->once()
            ->with(Mockery::type('Closure'))
            ->andReturnSelf();

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($query, $result);
    }

    #[Test]
    public function apply_search_returns_query_when_no_searchable_columns(): void
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        Config::set('simple-datatables-and-forms.search.default_mode', 'like');

        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        // Create non-searchable columns
        $column1 = new TextColumn('name');
        // Don't make it searchable

        $table->shouldReceive('getColumns')->andReturn([$column1]);

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($query, $result);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function can_use_fulltext_returns_false_for_non_mysql(): void
    {
        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('getDriverName')->andReturn('sqlite');
        $query->shouldReceive('getConnection')->andReturn($connection);

        $reflection = new ReflectionClass($this->searchService);
        $method = $reflection->getMethod('canUseFulltext');

        $result = $method->invoke($this->searchService, $query, $table);

        $this->assertFalse($result);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function can_use_fulltext_returns_false_when_no_table_name(): void
    {
        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('getDriverName')->andReturn('mysql');
        $query->shouldReceive('getConnection')->andReturn($connection);

        $queryBuilder = Mockery::mock(QueryBuilder::class);
        $queryBuilder->from = null;
        $query->shouldReceive('getQuery')->andReturn($queryBuilder);

        $reflection = new ReflectionClass($this->searchService);
        $method = $reflection->getMethod('canUseFulltext');

        $result = $method->invoke($this->searchService, $query, $table);

        $this->assertFalse($result);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function can_use_fulltext_returns_false_when_no_searchable_columns(): void
    {
        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('getDriverName')->andReturn('mysql');
        $query->shouldReceive('getConnection')->andReturn($connection);

        $queryBuilder = Mockery::mock(QueryBuilder::class);
        $queryBuilder->from = 'users';
        $query->shouldReceive('getQuery')->andReturn($queryBuilder);

        // Create non-searchable columns
        $column1 = new TextColumn('name');
        $table->shouldReceive('getColumns')->andReturn([$column1]);

        $reflection = new ReflectionClass($this->searchService);
        $method = $reflection->getMethod('canUseFulltext');

        $result = $method->invoke($this->searchService, $query, $table);

        $this->assertFalse($result);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function apply_fulltext_search_mysql(): void
    {
        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('getDriverName')->andReturn('mysql');
        $query->shouldReceive('getConnection')->andReturn($connection);

        // Create searchable columns
        $column1 = new TextColumn('name');
        $column1->searchable();
        $column2 = new TextColumn('description');
        $column2->searchable();

        $table->shouldReceive('getColumns')->andReturn([$column1, $column2]);

        $query
            ->shouldReceive('whereRaw')
            ->with('MATCH(name,description) AGAINST(? IN BOOLEAN MODE)', ['test*'])
            ->once()
            ->andReturnSelf();

        $reflection = new ReflectionClass($this->searchService);
        $method = $reflection->getMethod('applyFulltextSearch');

        $result = $method->invoke($this->searchService, $query, 'test', $table);

        $this->assertSame($query, $result);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function apply_fulltext_search_postgresql(): void
    {
        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('getDriverName')->andReturn('pgsql');
        $query->shouldReceive('getConnection')->andReturn($connection);

        // Create searchable columns
        $column1 = new TextColumn('name');
        $column1->searchable();

        $table->shouldReceive('getColumns')->andReturn([$column1]);

        $query
            ->shouldReceive('where')
            ->once()
            ->with(Mockery::type('Closure'))
            ->andReturnSelf();

        $reflection = new ReflectionClass($this->searchService);
        $method = $reflection->getMethod('applyFulltextSearch');

        $result = $method->invoke($this->searchService, $query, 'test', $table);

        $this->assertSame($query, $result);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function apply_fulltext_search_returns_query_when_no_searchable_columns(): void
    {
        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('getDriverName')->andReturn('mysql');
        $query->shouldReceive('getConnection')->andReturn($connection);

        // Create non-searchable columns
        $column1 = new TextColumn('name');
        $table->shouldReceive('getColumns')->andReturn([$column1]);

        $reflection = new ReflectionClass($this->searchService);
        $method = $reflection->getMethod('applyFulltextSearch');

        $result = $method->invoke($this->searchService, $query, 'test', $table);

        $this->assertSame($query, $result);
    }
}
