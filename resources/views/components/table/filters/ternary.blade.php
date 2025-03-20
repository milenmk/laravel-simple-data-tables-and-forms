<div class="filter-ternary">
    <label for="{{ $filter->name }}" class="block text-sm font-medium text-gray-700">
        {{ $filter->label }}
    </label>
    @if ($filter->toggle)
        <div class="flex items-center"
        >
            <label class="relative h-6 w-12">
                <input
                    type="checkbox"
                    wire:model.live="filters.{{ $filter->name }}"
                    class="custom_switch peer absolute z-10 h-full w-full cursor-pointer opacity-0"
                    id="switch_{{ $filter->name }}"
                />
                <span
                    for="switch_{{ $filter->name }}"
                    class="dark:bg-dark dark:before:bg-white-dark block h-full rounded-full bg-[#ebedf2] before:absolute before:bottom-1 before:left-1 before:h-4 before:w-4 before:rounded-full before:bg-white before:transition-all before:duration-300 peer-checked:before:left-7 dark:peer-checked:before:bg-white peer-checked:bg-primary"
            ></span>
            </label>
        </div>
    @else
        <input
            type="checkbox"
            id="{{ $filter->name }}"
            wire:model.live="filters.{{ $filter->name }}"
            class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        />
    @endif
</div>
