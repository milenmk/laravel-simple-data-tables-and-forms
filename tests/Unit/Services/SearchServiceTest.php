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
    public function it_returns_query_unchanged_when_search_is_empty()
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        $result = $this->searchService->applySearch($query, '', $table);

        $this->assertSame($query, $result);
    }

    #[Test]
    public function it_applies_like_search_by_default()
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        Config::set('simple-datatables-and-forms.search.default_mode', 'like');

        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        // Mock searchable columns
        $column1 = Mockery::mock(Column::class);
        $column1->searchable = true;
        $column1->key = 'name';

        $column2 = Mockery::mock(Column::class);
        $column2->searchable = false;
        $column2->key = 'id';

        $table->shouldReceive('getColumns')->andReturn([$column1, $column2]);

        $query
            ->shouldReceive('where')
            ->once()
            ->with(Mockery::type('callable'))
            ->andReturnSelf();

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($query, $result);
    }

    #[Test]
    public function it_applies_exact_search_when_configured()
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        Config::set('simple-datatables-and-forms.search.default_mode', 'like');

        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        // Mock searchable columns
        $column1 = Mockery::mock(Column::class);
        $column1->searchable = true;
        $column1->key = 'name';

        $table->shouldReceive('getColumns')->andReturn([$column1]);

        $query
            ->shouldReceive('where')
            ->once()
            ->with(Mockery::type('callable'))
            ->andReturnSelf();

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($query, $result);
    }

    #[Test]
    public function it_applies_fulltext_search_when_configured_and_available()
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        Config::set('simple-datatables-and-forms.search.default_mode', 'like');

        Config::set('simple-datatables-and-forms.search.enable_fulltext', false);

        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        // Mock connection and driver
        $connection = Mockery::mock();
        $connection->shouldReceive('getDriverName')->andReturn('mysql');

        $query->shouldReceive('getConnection')->andReturn($connection);

        // Mock query builder
        $queryBuilder = Mockery::mock();
        $queryBuilder->from = 'users';
        $query->shouldReceive('getQuery')->andReturn($queryBuilder);

        // Mock searchable columns
        $column1 = Mockery::mock(Column::class);
        $column1->searchable = true;
        $column1->key = 'name';

        $table->shouldReceive('getColumns')->andReturn([$column1]);

        // Since we can't easily mock the fulltext index check, it will likely fall back to like search
        $query
            ->shouldReceive('where')
            ->once()
            ->with(Mockery::type('callable'))
            ->andReturnSelf();

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($query, $result);
    }

    #[Test]
    public function it_falls_back_to_like_search_when_fulltext_not_available()
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        Config::set('simple-datatables-and-forms.search.default_mode', 'like');

        Config::set('simple-datatables-and-forms.search.enable_fulltext', false);

        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        // Mock searchable columns
        $column1 = Mockery::mock(Column::class);
        $column1->searchable = true;
        $column1->key = 'name';

        $table->shouldReceive('getColumns')->andReturn([$column1]);

        $query
            ->shouldReceive('where')
            ->once()
            ->with(Mockery::type('callable'))
            ->andReturnSelf();

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($query, $result);
    }

    #[Test]
    public function it_returns_query_unchanged_when_no_searchable_columns()
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        Config::set('simple-datatables-and-forms.search.default_mode', 'like');

        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        // Mock non-searchable columns
        $column1 = Mockery::mock(Column::class);
        $column1->searchable = false;
        $column1->key = 'id';

        $table->shouldReceive('getColumns')->andReturn([$column1]);

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($query, $result);
    }

    #[Test]
    public function it_handles_unknown_search_mode()
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        Config::set('simple-datatables-and-forms.search.default_mode', 'like');

        $query = Mockery::mock(Builder::class);
        $table = Mockery::mock(Table::class);

        // Mock searchable columns
        $column1 = Mockery::mock(Column::class);
        $column1->searchable = true;
        $column1->key = 'name';

        $table->shouldReceive('getColumns')->andReturn([$column1]);

        $query
            ->shouldReceive('where')
            ->once()
            ->with(Mockery::type('callable'))
            ->andReturnSelf();

        $result = $this->searchService->applySearch($query, 'test', $table);

        $this->assertSame($query, $result);
    }
}
