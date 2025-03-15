<?php

namespace Milenmk\LaravelSimpleDatatables\Traits;

use Livewire\Attributes\On;

trait WithGrouping
{
    public ?string $selectedGroup = null;
    public array $collapsedGroups = [];

    public function mountWithGrouping(): void
    {
        $this->listeners['groupingChanged'] = 'updateSelectedGroup';
    }

    public function updatedSelectedGroup($value): void
    {
        $this->debounceUpdateSelectedGroup($value);
    }

    #[On('debounceUpdateSelectedGroup')]
    public function debounceUpdateSelectedGroup(string $group): void
    {
        $this->selectedGroup = $group;
        $this->resetPage();
    }

    public function toggleGroupVisibility(string $groupKey): void
    {
        if (in_array($groupKey, $this->collapsedGroups)) {
            $this->collapsedGroups = array_filter($this->collapsedGroups, function ($key) use ($groupKey) {
                return $key !== $groupKey;
            });
        } else {
            $this->collapsedGroups[] = $groupKey;
        }
    }
}
