<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Traits;

use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\WithGrouping;
use PHPUnit\Framework\Attributes\Test;

class WithGroupingTest extends BaseTest
{
    private object $testClass;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an anonymous class that uses the WithGrouping trait
        $this->testClass = new class
        {
            use WithGrouping;

            public array $listeners = [];

            public function resetPage(): void
            {
                // Mock resetPage method
            }
        };
    }

    #[Test]
    public function it_initializes_with_default_values()
    {
        $this->assertNull($this->testClass->selectedGroup);
        $this->assertEquals([], $this->testClass->collapsedGroups);
    }

    #[Test]
    public function it_can_mount_with_grouping()
    {
        $this->testClass->mountWithGrouping();

        $this->assertArrayHasKey('groupingChanged', $this->testClass->listeners);
        $this->assertEquals('updateSelectedGroup', $this->testClass->listeners['groupingChanged']);
    }

    #[Test]
    public function it_can_update_selected_group()
    {
        $this->testClass->updatedSelectedGroup('category');

        // The method calls debounceUpdateSelectedGroup, which we can't easily test
        // without Livewire's event system, so we just verify it doesn't throw
        $this->assertTrue(true);
    }

    #[Test]
    public function it_can_debounce_update_selected_group()
    {
        $this->testClass->debounceUpdateSelectedGroup('category');

        $this->assertEquals('category', $this->testClass->selectedGroup);
    }

    #[Test]
    public function it_can_toggle_group_visibility_to_collapse()
    {
        $this->assertEquals([], $this->testClass->collapsedGroups);

        $this->testClass->toggleGroupVisibility('group1');

        $this->assertContains('group1', $this->testClass->collapsedGroups);
    }

    #[Test]
    public function it_can_toggle_group_visibility_to_expand()
    {
        $this->testClass->collapsedGroups = ['group1', 'group2'];

        $this->testClass->toggleGroupVisibility('group1');

        $this->assertNotContains('group1', $this->testClass->collapsedGroups);
        $this->assertContains('group2', $this->testClass->collapsedGroups);
    }

    #[Test]
    public function it_handles_toggling_non_existent_group()
    {
        $this->testClass->collapsedGroups = ['group1'];

        $this->testClass->toggleGroupVisibility('group2');

        $this->assertContains('group1', $this->testClass->collapsedGroups);
        $this->assertContains('group2', $this->testClass->collapsedGroups);
    }

    #[Test]
    public function it_maintains_other_collapsed_groups_when_toggling()
    {
        $this->testClass->collapsedGroups = ['group1', 'group2', 'group3'];

        $this->testClass->toggleGroupVisibility('group2');

        $this->assertContains('group1', $this->testClass->collapsedGroups);
        $this->assertNotContains('group2', $this->testClass->collapsedGroups);
        $this->assertContains('group3', $this->testClass->collapsedGroups);
    }

    #[Test]
    public function it_can_handle_empty_group_key()
    {
        $this->testClass->toggleGroupVisibility('');

        $this->assertContains('', $this->testClass->collapsedGroups);
    }

    #[Test]
    public function it_can_handle_multiple_toggles_of_same_group()
    {
        // Collapse
        $this->testClass->toggleGroupVisibility('group1');
        $this->assertContains('group1', $this->testClass->collapsedGroups);

        // Expand
        $this->testClass->toggleGroupVisibility('group1');
        $this->assertNotContains('group1', $this->testClass->collapsedGroups);

        // Collapse again
        $this->testClass->toggleGroupVisibility('group1');
        $this->assertContains('group1', $this->testClass->collapsedGroups);
    }
}
