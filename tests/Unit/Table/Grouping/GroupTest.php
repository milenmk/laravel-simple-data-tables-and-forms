<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Table\Grouping;

use Milenmk\LaravelSimpleDatatablesAndForms\Table\Grouping\Group;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;

class GroupTest extends BaseTest
{
    #[Test]
    public function it_can_be_instantiated_with_key()
    {
        $group = (new Group('category'))->label('Category');

        $this->assertSame('category', $group->key);
        $this->assertSame('Category', $group->label);
    }

    #[Test]
    public function it_can_be_created_using_make_method()
    {
        $group = Group::make('category')->label('Category');

        $this->assertInstanceOf(Group::class, $group);
        $this->assertSame('category', $group->key);
        $this->assertSame('Category', $group->label);
    }

    #[Test]
    public function it_can_be_created_with_custom_label()
    {
        $group = Group::make('category')->label('Product Category');

        $this->assertSame('category', $group->key);
        $this->assertSame('Product Category', $group->label);
    }

    #[Test]
    public function it_can_set_and_get_label()
    {
        $group = new Group('category');

        $group->label('Custom Category');

        $this->assertSame('Custom Category', $group->label);
    }
}
