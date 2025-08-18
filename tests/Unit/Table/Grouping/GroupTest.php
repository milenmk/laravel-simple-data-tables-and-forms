<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Grouping;

use Milenmk\LaravelSimpleDatatablesAndForms\Table\Grouping\Group;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;

class GroupTest extends BaseTest
{
    #[Test]
    public function it_can_be_created_with_key()
    {
        $group = new Group('category');

        $this->assertEquals('category', $group->key);
        $this->assertEquals('category', $group->label);
        $this->assertFalse($group->collapsible);
    }

    #[Test]
    public function it_can_be_created_using_make_method()
    {
        $group = Group::make('status');

        $this->assertEquals('status', $group->key);
        $this->assertEquals('status', $group->label);
        $this->assertFalse($group->collapsible);
    }

    #[Test]
    public function it_can_set_custom_label()
    {
        $group = Group::make('category');

        $result = $group->label('Product Category');

        $this->assertEquals('Product Category', $group->label);
        $this->assertSame($group, $result); // Test fluent interface
    }

    #[Test]
    public function it_can_set_collapsible_to_true()
    {
        $group = Group::make('category');

        $result = $group->collapsible();

        $this->assertTrue($group->collapsible);
        $this->assertSame($group, $result); // Test fluent interface
    }

    #[Test]
    public function it_can_set_collapsible_to_false()
    {
        $group = Group::make('category');

        $result = $group->collapsible(false);

        $this->assertFalse($group->collapsible);
        $this->assertSame($group, $result); // Test fluent interface
    }

    #[Test]
    public function it_can_chain_methods()
    {
        $group = Group::make('category')
            ->label('Product Categories')
            ->collapsible(true);

        $this->assertEquals('category', $group->key);
        $this->assertEquals('Product Categories', $group->label);
        $this->assertTrue($group->collapsible);
    }
}
