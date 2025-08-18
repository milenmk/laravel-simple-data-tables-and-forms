<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Milenmk\LaravelSimpleDatatablesAndForms\Exceptions\InvalidFilterTypeException;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\TextColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters\Filter;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Grouping\Group;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class TableTest extends BaseTest
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_be_created()
    {
        $table = new Table;

        $this->assertEquals('', $table->heading);
        $this->assertNull($table->striped);
        $this->assertNull($table->showFilters);
        $this->assertEquals([], $table->extraAttributes);
    }

    #[Test]
    public function it_can_set_query_with_builder()
    {
        $table = new Table;
        $query = Mockery::mock(Builder::class);

        $result = $table->query($query);

        $this->assertSame($table, $result); // Test fluent interface
    }

    #[Test]
    public function it_can_set_query_with_paginator()
    {
        $table = new Table;
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $result = $table->query($paginator);

        $this->assertSame($table, $result); // Test fluent interface
    }

    #[Test]
    public function it_can_set_columns()
    {
        $table = new Table;
        $columns = [TextColumn::make('name'), TextColumn::make('email')];

        $result = $table->schema($columns);

        $this->assertEquals($columns, $table->getColumns());
        $this->assertSame($table, $result);
    }

    #[Test]
    public function it_can_set_heading_as_string()
    {
        $table = new Table;
        $result = $table->heading('Users Table');

        $this->assertEquals('Users Table', $table->heading);
        $this->assertSame($table, $result);
    }

    #[Test]
    public function it_can_set_heading_as_array()
    {
        $table = new Table;
        $headings = ['en' => 'Users', 'es' => 'Usuarios'];

        $result = $table->heading($headings);

        $this->assertEquals($headings, $table->heading);
        $this->assertSame($table, $result);
    }

    #[Test]
    public function it_can_set_striped()
    {
        $table = new Table;

        // Test enabling striped
        $result = $table->striped();
        $this->assertTrue($table->striped);
        $this->assertSame($table, $result);

        // Test disabling striped
        $table->striped(false);
        $this->assertFalse($table->striped);
    }

    #[Test]
    public function it_can_set_show_filters()
    {
        $table = new Table;

        // Test enabling show filters
        $result = $table->setShowFilters(true);
        $this->assertTrue($table->showFilters);
        $this->assertSame($table, $result);

        // Test disabling show filters
        $table->setShowFilters(false);
        $this->assertFalse($table->showFilters);
    }

    #[Test]
    public function it_can_set_model_class()
    {
        $table = new Table;
        $result = $table->model('App\\Models\\User');

        $this->assertSame($table, $result);
    }

    /**
     * @throws InvalidFilterTypeException
     */
    #[Test]
    public function it_can_set_filters()
    {
        $table = new Table;
        $filters = [Filter::make('status'), Filter::make('category')];

        $result = $table->filters($filters);

        $this->assertSame($table, $result);
    }

    #[Test]
    public function it_can_set_groups()
    {
        $table = new Table;
        $groups = [Group::make('category'), Group::make('status')];

        $result = $table->groups($groups);

        $this->assertEquals($groups, $table->getGroups());
        $this->assertSame($table, $result);
    }

    #[Test]
    public function it_can_get_groups()
    {
        $table = new Table;
        $groups = [Group::make('category'), Group::make('status')];

        $table->groups($groups);

        $this->assertEquals($groups, $table->getGroups());
    }

    #[Test]
    public function it_can_set_extra_attributes_as_array()
    {
        $table = new Table;
        $attributes = ['class' => 'custom-table', 'data-test' => 'value'];

        $result = $table->extraAttributes($attributes);

        $this->assertEquals($attributes, $table->extraAttributes);
        $this->assertSame($table, $result);
    }

    #[Test]
    public function it_can_set_extra_attributes_as_closure()
    {
        $table = new Table;
        $closure = fn () => ['class' => 'dynamic-table'];

        $result = $table->extraAttributes($closure);

        $this->assertEquals($closure, $table->extraAttributesCallback);
        $this->assertSame($table, $result);
    }

    #[Test]
    public function it_can_set_filter_columns()
    {
        $table = new Table;
        $result = $table->filterColumns(3);

        $this->assertSame($table, $result);
    }

    #[Test]
    public function it_can_set_filter_responsive()
    {
        $table = new Table;
        $result = $table->filterResponsive();

        $this->assertSame($table, $result);
    }

    #[Test]
    public function it_can_set_filter_responsive_columns()
    {
        $table = new Table;
        $columns = ['sm' => 1, 'md' => 2, 'lg' => 3];

        $result = $table->filterResponsiveColumns($columns);

        $this->assertSame($table, $result);
    }

    #[Test]
    public function it_can_get_merged_attributes()
    {
        $table = new Table;
        $table->extraAttributes(['class' => 'extra-class', 'data-test' => 'value']);

        $baseAttributes = ['class' => 'base-class', 'id' => 'test-id'];
        $merged = $table->getMergedAttributes($baseAttributes);

        $this->assertEquals('base-class extra-class', $merged['class']);
        $this->assertEquals('test-id', $merged['id']);
        $this->assertEquals('value', $merged['data-test']);
    }

    #[Test]
    public function it_can_get_extra_attributes()
    {
        $table = new Table;
        $attributes = ['class' => 'test-class'];
        $table->extraAttributes($attributes);

        $this->assertEquals($attributes, $table->getExtraAttributes());
    }

    #[Test]
    public function it_can_get_extra_attributes_with_callback()
    {
        $table = new Table;
        $table->extraAttributes(fn () => ['class' => 'callback-class']);

        $attributes = $table->getExtraAttributes();

        $this->assertEquals('callback-class', $attributes['class']);
    }

    #[Test]
    public function it_can_get_query()
    {
        $table = new Table;
        $query = Mockery::mock(Builder::class);
        $table->query($query);

        $this->assertSame($query, $table->getQuery());
    }

    #[Test]
    public function it_returns_null_query_initially()
    {
        $table = new Table;

        $this->assertNull($table->getQuery());
    }

    #[Test]
    public function it_can_get_data()
    {
        $table = new Table;
        $query = Mockery::mock(Builder::class);
        $table->query($query);

        $this->assertSame($query, $table->data());
    }

    #[Test]
    public function it_can_get_model_class()
    {
        $table = new Table;
        $modelClass = 'App\\Models\\User';
        $table->model($modelClass);

        $this->assertEquals($modelClass, $table->getModelClass());
    }

    #[Test]
    public function it_returns_null_model_class_initially()
    {
        $table = new Table;

        $this->assertNull($table->getModelClass());
    }

    #[Test]
    public function it_can_get_model_instance()
    {
        $table = new Table;
        $table->model('Milenmk\\LaravelSimpleDatatablesAndForms\\Tests\\Fixtures\\User');

        // This will return null since the model doesn't exist, but tests the method
        $instance = $table->getModelInstance('1');
        $this->assertNull($instance);
    }

    #[Test]
    public function it_returns_null_model_instance_when_no_model_class()
    {
        $table = new Table;

        $instance = $table->getModelInstance('1');
        $this->assertNull($instance);
    }

    /**
     * @throws InvalidFilterTypeException
     */
    #[Test]
    public function it_can_get_filters()
    {
        $table = new Table;
        $filters = [Filter::make('status'), Filter::make('category')];
        $table->filters($filters);

        $this->assertEquals($filters, $table->getFilters());
    }

    #[Test]
    public function it_returns_empty_filters_initially()
    {
        $table = new Table;

        $this->assertEquals([], $table->getFilters());
    }

    #[Test]
    public function it_can_get_filters_groups()
    {
        $table = new Table;

        $this->assertEquals([], $table->getFiltersGroups());
    }

    #[Test]
    public function it_can_set_and_get_filters_values()
    {
        $table = new Table;
        $filtersValues = ['status' => 'active', 'category' => 'electronics'];

        $result = $table->setFiltersValues($filtersValues);

        $this->assertSame($table, $result);
        $this->assertEquals($filtersValues, $table->getFiltersValues());
    }

    #[Test]
    public function it_returns_empty_filters_values_initially()
    {
        $table = new Table;

        $this->assertEquals([], $table->getFiltersValues());
    }

    #[Test]
    public function it_can_get_selected_group_from_trait()
    {
        $table = new Table;

        $this->assertNull($table->getSelectedGroupFromTrait());
    }

    #[Test]
    public function it_can_set_selected_group_from_trait()
    {
        $table = new Table;
        $selectedGroup = 'category';

        $result = $table->setSelectedGroupFromTrait($selectedGroup);

        $this->assertSame($table, $result);
        $this->assertEquals($selectedGroup, $table->getSelectedGroupFromTrait());
    }

    #[Test]
    public function it_can_get_collapsed_groups_from_trait()
    {
        $table = new Table;

        $this->assertEquals([], $table->getCollapsedGroupsFromTrait());
    }

    #[Test]
    public function it_can_set_collapsed_groups_from_trait()
    {
        $table = new Table;
        $collapsedGroups = ['group1', 'group2'];

        $result = $table->setCollapsedGroupsFromTrait($collapsedGroups);

        $this->assertSame($table, $result);
        $this->assertEquals($collapsedGroups, $table->getCollapsedGroupsFromTrait());
    }

    #[Test]
    public function it_can_set_table_filters()
    {
        $table = new Table;
        $tableFilters = ['filter1', 'filter2'];

        $result = $table->setTableFilters($tableFilters);

        $this->assertSame($table, $result);
    }

    #[Test]
    public function it_can_get_filter_columns_with_default()
    {
        $table = new Table;

        // Should return config default (6)
        $this->assertEquals(6, $table->getFilterColumns());
    }

    #[Test]
    public function it_can_get_filter_responsive_with_default()
    {
        $table = new Table;

        // Should return config default (true)
        $this->assertTrue($table->getFilterResponsive());
    }

    #[Test]
    public function it_can_get_filter_responsive_columns_with_default()
    {
        $table = new Table;

        $expected = [
            'sm' => 1,
            'md' => 2,
            'lg' => 4,
            'xl' => 6,
        ];

        $this->assertEquals($expected, $table->getFilterResponsiveColumns());
    }

    #[Test]
    public function it_can_create_groups_from_arrays()
    {
        $table = new Table;
        $groups = [['category', 'Category'], ['status', 'Status']];

        $table->groups($groups);

        $resultGroups = $table->getGroups();
        $this->assertCount(2, $resultGroups);
        $this->assertInstanceOf(Group::class, $resultGroups[0]);
        $this->assertInstanceOf(Group::class, $resultGroups[1]);
    }

    #[Test]
    public function it_can_create_groups_from_strings()
    {
        $table = new Table;
        $groups = ['category', 'status'];

        $table->groups($groups);

        $resultGroups = $table->getGroups();
        $this->assertCount(2, $resultGroups);
        $this->assertInstanceOf(Group::class, $resultGroups[0]);
        $this->assertInstanceOf(Group::class, $resultGroups[1]);
    }
}
