<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\TextColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Grouping\Group;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

class TableTest extends BaseTest
{
    protected Table $table;

    protected function setUp(): void
    {
        parent::setUp();

        $this->table = new Table;
    }

    #[Test]
    public function it_can_set_and_get_query()
    {
        $query = TestModel::query();

        $this->table->query($query);

        $this->assertInstanceOf(Builder::class, $this->table->getQuery());
        $this->assertSame($query, $this->table->getQuery());
    }

    #[Test]
    public function it_can_set_and_get_schema()
    {
        $columns = [TextColumn::make('name')->label('Name'), TextColumn::make('email')->label('Email')];

        $this->table->schema($columns);

        $this->assertSame($columns, $this->table->getColumns());
    }

    #[Test]
    public function it_can_set_and_get_heading()
    {
        $heading = 'Test Heading';

        $this->table->heading($heading);

        $this->assertSame($heading, $this->table->heading);
    }

    #[Test]
    public function it_can_set_and_get_striped()
    {
        $this->table->striped();

        $this->assertTrue($this->table->striped);

        $this->table->striped(false);

        $this->assertFalse($this->table->striped);
    }

    #[Test]
    public function it_can_set_and_get_groups()
    {
        $groups = [Group::make('category')->label('Category'), Group::make('is_active')->label('Status')];

        $this->table->groups($groups);

        $this->assertEquals($groups, $this->table->getGroups());
    }

    #[Test]
    public function it_can_set_and_get_model_class()
    {
        $modelClass = TestModel::class;

        $this->table->model($modelClass);

        $this->assertSame($modelClass, $this->table->getModelClass());
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function it_can_prepare_view_data_for_rendering()
    {
        // Create test data
        TestModel::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_active' => true,
        ]);

        $query = TestModel::query()->paginate(10);

        $columns = [TextColumn::make('name')->label('Name'), TextColumn::make('email')->label('Email')];

        $table = $this->table
            ->query($query)
            ->schema($columns)
            ->heading('Test Table');

        // Get the view data without rendering the view
        $view = $table->render();
        $viewData = $view->getData();

        // Test that the view data is correctly prepared
        $this->assertNotNull($viewData);
        $this->assertEquals('Test Table', $viewData['heading']);
        $this->assertEquals($columns, $viewData['columns']);
        $this->assertInstanceOf(LengthAwarePaginator::class, $viewData['data']);
        $this->assertTrue($viewData['striped']); // From config in BaseTest
        $this->assertTrue($viewData['hover']); // From config in BaseTest
        $this->assertEquals('all', $viewData['borders']); // From config in BaseTest
        $this->assertEquals('md', $viewData['size']); // From config in BaseTest
        $this->assertEquals('light', $viewData['theme']); // From config in BaseTest

        // We're not actually rendering the view to avoid the Livewire dependency issues
        // This is a valid approach for unit testing since we're testing the Table class functionality,
        // not the actual rendering of the Blade template
    }

    #[Test]
    public function it_can_get_model_instance()
    {
        $model = TestModel::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_active' => true,
        ]);

        $this->table->model(TestModel::class);

        $retrievedModel = $this->table->getModelInstance($model->id);

        $this->assertInstanceOf(TestModel::class, $retrievedModel);
        $this->assertEquals($model->id, $retrievedModel->id);
    }

    #[Test]
    public function it_returns_null_for_invalid_model_instance()
    {
        $this->table->model('InvalidModel');

        $retrievedModel = $this->table->getModelInstance(1);

        $this->assertNull($retrievedModel);
    }

    #[Test]
    public function it_can_set_and_get_selected_group()
    {
        $selectedGroup = 'category';

        $this->table->setSelectedGroupFromTrait($selectedGroup);

        $this->assertSame($selectedGroup, $this->table->getSelectedGroupFromTrait());
    }

    #[Test]
    public function it_can_set_and_get_collapsed_groups()
    {
        $collapsedGroups = ['category1', 'category2'];

        $this->table->setCollapsedGroupsFromTrait($collapsedGroups);

        $this->assertSame($collapsedGroups, $this->table->getCollapsedGroupsFromTrait());
    }
}
