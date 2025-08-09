@props([
    "heading" => null,
    "columns" => null,
    "data" => null,
    "striped" => false,
    "hover" => true,
    "borders" => "all",
    "size" => "md",
    "theme" => "light",
    "groups" => null,
    "selectedGroup" => null,
    "collapsedGroups" => [],
    "showFilters" => false,
    "tableFilters" => [],
    "filters" => [],
    "csrfField" => "",
    "exportEnabled" => true,
    "exportFormats" => ["csv"],
    "filterColumns" => 6,
    "filterResponsive" => true,
    "filterResponsiveColumns" => [
        "sm" => 1,
        "md" => 2,
        "lg" => 4,
        "xl" => 6,
    ],
])

@php
    // Generate grid classes based on configuration
    $gridClasses = "grid gap-4";

    if ($filterResponsive) {
        // Add responsive grid classes
        $gridClasses .= " grid-cols-{$filterResponsiveColumns["sm"]}";
        $gridClasses .= " sm:grid-cols-{$filterResponsiveColumns["md"]}";
        $gridClasses .= " md:grid-cols-{$filterResponsiveColumns["lg"]}";
        $gridClasses .= " lg:grid-cols-{$filterResponsiveColumns["xl"]}";
    } else {
        // Use fixed columns
        $gridClasses .= " grid-cols-{$filterColumns}";
    }
@endphp

<div class="panel">
    @if ($heading)
        <h5 class="dark:text-white-light mb-5 text-lg font-semibold md:top-[25px] md:mb-5">
            {{ $heading }}
        </h5>
    @endif

    <div class="relative">
        <div class="mb-4 flex flex-wrap items-center justify-between">
            <div
                class="relative mr-2 mb-2 sm:mb-0"
                x-data="{ columnDropdown: false }"
                @click.outside="columnDropdown = false"
            >
                <a
                    href="javascript:"
                    class="inline-flex w-full min-w-[100px] items-center rounded border border-gray-300 bg-white p-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                    @click="columnDropdown = ! columnDropdown"
                >
                    <span class="ltr:mr-1 rtl:ml-1">{{ __("Columns") }}</span>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M19 9L12 15L5 9"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        ></path>
                    </svg>
                </a>
                <div
                    class="text-dark dark:text-white-light absolute top-11 z-[10] hidden w-[100px] min-w-[200px] rounded bg-white py-2 shadow ltr:left-0 rtl:right-0 dark:bg-[#1b2e4b]"
                    :class="columnDropdown &amp;&amp; '!block'"
                >
                    <ul class="space-y-2 px-4 font-semibold">
                        @foreach ($columns as $column)
                            <li>
                                <div>
                                    <label class="flex cursor-pointer items-center">
                                        <input
                                            type="checkbox"
                                            class="form-checkbox h-4 w-4"
                                            id="checkbox-{{ $column->key }}"
                                            wire:change="toggleColumnVisibility('{{ $column->key }}')"
                                            {{ $column->visible ? "checked" : "" }}
                                        />
                                        <span class="ltr:ml-2 rtl:mr-2" for="{{ $column->key }}">
                                            {{ $column->label }}
                                        </span>
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="mb-2 flex space-x-2 sm:mb-0">
                <select
                    wire:model.live="selectedGroup"
                    key="grouping-select"
                    class="block w-full rounded border border-gray-300 bg-white p-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                >
                    <option value="">{{ __("No Grouping") }}</option>
                    @if (isset($groups))
                        @foreach ($groups as $group)
                            <option value="{{ $group->key }}">{{ __($group->label) }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="ml-auto flex flex-wrap">
                @if ($exportEnabled)
                    <div class="mr-2 mb-2 sm:mb-0">
                        <x-laravel-simple-datatables::export :formats="$exportFormats" />
                    </div>
                @endif

                <div class="dataTable-search mb-2 sm:mb-0">
                    <div
                        x-data="{
                            search: @entangle("search").defer || '',
                            debounceTime: {{ $this->searchDebounceTime ?? 300 }},
                            minChars: {{ $this->searchMinCharacters ?? 2 }},
                        }"
                        class="relative w-full items-center"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="absolute top-2.5 left-2.5 h-5 w-5 text-slate-600 opacity-50"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <input
                            x-model="search"
                            x-on:input.debounce="$wire.set('search', search)"
                            x-bind:input.debounce="debounceTime + 'ms'"
                            class="dataTable-input ease w-full rounded-md border border-slate-200 bg-transparent py-2 pr-10 pl-10 text-sm text-slate-700 shadow-sm transition duration-300 placeholder:text-slate-400 hover:border-slate-300 focus:border-slate-400 focus:shadow focus:outline-none"
                            placeholder="Search..."
                            type="text"
                            style="padding-left: 2rem"
                        />
                        <button
                            type="button"
                            x-on:click="
                                search = ''
                                $wire.set('search', '')
                            "
                            x-show="search.length > 0"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-xl text-gray-500 hover:text-gray-700 focus:outline-none"
                        >
                            &times;
                        </button>

                        <div
                            class="absolute right-0 mt-1 flex space-x-1 text-xs"
                            x-show="search.length > 0 && search.length < minChars"
                        >
                            <span class="text-red-500" x-show="search.length > 0 && search.length < minChars">
                                {{ __("Min") }}
                                <span x-text="minChars"></span>
                                {{ __("characters") }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="ml-2">
                    <button
                        type="button"
                        wire:click="toggleFilters"
                        class="mb-2 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none sm:mb-0"
                    >
                        {{ __("Filters") }} ({{ $this->getAppliedFiltersCount() }})
                    </button>
                </div>
            </div>
        </div>

        <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
            <div class="dataTable-top">
                <!-- Filters Panel -->
                @if ($showFilters)
                    <div class="mb-4 w-full">
                        <div class="mt-4 rounded-md border bg-gray-50 p-4 dark:bg-(--color-white-dark)">
                            <div class="{{ $gridClasses }}">
                                <!-- Dynamic grid container -->
                                @foreach ($tableFilters as $filter)
                                    @if ($filter["isGroup"] ?? false)
                                        <!-- Grouped filters in a single column -->
                                        <div class="space-y-3">
                                            @if (isset($filter["groupLabel"]) && $filter["groupLabel"] !== "")
                                                <div class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ $filter["groupLabel"] }}
                                                </div>
                                            @endif

                                            @foreach ($filter["filters"] as $groupedFilter)
                                                {!! $groupedFilter["view"] !!}
                                            @endforeach
                                        </div>
                                    @else
                                        <!-- Single filter -->
                                        {!! $filter["view"] !!}
                                    @endif
                                @endforeach
                            </div>
                            <div class="mt-4 flex justify-end">
                                <!-- Reset Button -->
                                <button
                                    type="button"
                                    wire:click="resetFilters"
                                    class="mt-2 inline-flex items-center rounded-md border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none"
                                >
                                    {{ __("Reset Filters") }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Active Filters Display -->
            @if (! empty($filters))
                <div class="mb-4 flex flex-wrap items-center">
                    @foreach ($filters as $filterName => $filterValues)
                        @if (! empty($filterValues))
                            @php
                                // First try to find the filter directly
                                $filterItem = collect($tableFilters)->firstWhere("name", $filterName);

                                // If not found, search within grouped filters
                                if (! $filterItem) {
                                    foreach ($tableFilters as $tableFilter) {
                                        if (isset($tableFilter["isGroup"]) && $tableFilter["isGroup"]) {
                                            $groupedFilter = collect($tableFilter["filters"])->firstWhere("name", $filterName);
                                            if ($groupedFilter) {
                                                $filterItem = $groupedFilter;
                                                break;
                                            }
                                        }
                                    }
                                }

                                $filterLabel = $filterItem["filterLabel"] ?? $filterName;
                                $filterOptions = $filterItem["filterOptions"] ?? [];
                            @endphp

                            @if (is_array($filterValues))
                                @foreach ($filterValues as $filterValue)
                                    <div
                                        class="mr-2 mb-2 inline-flex items-center rounded-full bg-gray-200 px-3 py-1 text-sm font-medium dark:bg-gray-700"
                                    >
                                        <span class="mr-2">
                                            {{ $filterLabel }}: {{ $filterOptions[$filterValue] ?? $filterValue }}
                                        </span>
                                        <button
                                            wire:click="removeFilter('{{ $filterName }}', '{{ $filterValue }}')"
                                            type="button"
                                            class="text-gray-500 hover:text-gray-700 focus:outline-none"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"
                                                ></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                @php
                                    $boolValue = $filterValues === true ? "Yes" : ($filterValues === false ? "No" : null);
                                    $displayValue = $boolValue ?? ($filterOptions[$filterValues] ?? $filterValues);
                                @endphp

                                <div
                                    class="mr-2 mb-2 inline-flex items-center rounded-full bg-gray-200 px-3 py-1 text-sm font-medium dark:bg-gray-700"
                                >
                                    <span class="mr-2">{{ $filterLabel }}: {{ $displayValue }}</span>
                                    <button
                                        wire:click="removeFilter('{{ $filterName }}', '{{ $filterValues }}')"
                                        type="button"
                                        class="text-gray-500 hover:text-gray-700 focus:outline-none"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>
            @endif

            <div class="dataTable-container">
                <div class="overflow-x-auto">
                    <table
                        id="myTable"
                        @class([
                            "dataTable-table min-w-full divide-y divide-gray-200",
                            "table-striped" => $striped,
                            "border-collapse border border-gray-200" => $borders == "all",
                            "border-collapse border-x border-gray-200" => $borders == "horizontal",
                            "border-collapse border-y border-gray-200" => $borders == "vertical",
                            "border-collapse border-0" => $borders == "none",
                            "text-xs" => $size == "sm",
                            "text-sm" => $size == "md",
                            "text-base" => $size == "lg",
                            "dark:bg-gray-800 dark:text-gray-100" => $theme == "dark",
                        ])
                    >
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach ($columns as $column)
                                    @if ($column->visible)
                                        <th
                                            id="th-{{ $column->key }}"
                                            @class([
                                                "items-center px-4 py-3 font-medium tracking-wider text-gray-500 uppercase",
                                                "asc" => $this->sortDir === "ASC" && $this->sortField === $column->key,
                                                "desc" => $this->sortDir === "DESC" && $this->sortField === $column->key,
                                            ])
                                            data-sortable
                                            @if ($column->sortable)
                                                wire:click="setSortBy('{{ $column->key }}')"
                                            @endif
                                            style="text-align: {{ $column->headerAlign }}"
                                        >
                                            <span
                                                @class([
                                                    "dataTable-sorter cursor-pointer" => $column->sortable === true,
                                                ])
                                            >
                                                {{ $column->label }}
                                            </span>
                                        </th>
                                    @endif
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @if ($selectedGroup && isset($groups))
                                @php
                                    $groupData = $data->groupBy($selectedGroup);
                                    $groupCollection = collect($groups);
                                @endphp

                                @foreach ($groupData as $groupKey => $items)
                                    <tr class="group-header">
                                        <td colspan="{{ count($columns) }}">
                                            <button
                                                wire:click="toggleGroupVisibility('{{ $groupKey }}')"
                                                type="button"
                                            >
                                                {{ __($groupCollection->firstWhere("key", $selectedGroup)->label ?? "") }}
                                                :
                                                @if ($groupKey === 1)
                                                    {{ __("Yes") }}
                                                @elseif ($groupKey === 0)
                                                    {{ __("No") }}
                                                @else
                                                    {{ __($groupKey) }}
                                                @endif
                                                @if (in_array($groupKey, $collapsedGroups))
                                                    <span>&#9654;</span>
                                                    <!-- Right arrow when collapsed -->
                                                @else
                                                    <span>&#9660;</span>
                                                    <!-- Down arrow when expanded -->
                                                @endif
                                            </button>
                                        </td>
                                    </tr>
                                    @if (! in_array($groupKey, $collapsedGroups))
                                        @foreach ($items as $item)
                                            <tr wire:key="{{ $item->id }}" @class(["hover:bg-gray-200" => $hover])>
                                                @foreach ($columns as $column)
                                                    @if ($column->visible)
                                                        @include(
                                                            $column->getView(),
                                                            [
                                                                "item" => $item,
                                                                "column" => $column,
                                                            ]
                                                        )
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            @else
                                <!-- Render normal rows -->
                                @forelse ($data as $item)
                                    <tr wire:key="{{ $item->id }}" @class(["hover:bg-gray-200" => $hover])>
                                        @foreach ($columns as $column)
                                            @if ($column->visible)
                                                @include(
                                                    $column->getView(),
                                                    [
                                                        "item" => $item,
                                                        "column" => $column,
                                                    ]
                                                )
                                            @endif
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($columns) }}">
                                            <div class="flex items-center justify-center space-x-2">
                                                <img
                                                    src="data:image/png;base64,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"
                                                    alt="{{ __("noData") }}"
                                                />
                                            </div>
                                            <div class="flex items-center justify-center space-x-2 text-gray-400">
                                                <p>{{ __("No Data") }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="dataTable-bottom mt-4">
                    {{ $data->links("laravel-simple-datatables::components.pagination.tailwind") }}
                </div>

                @if (! empty($csrfField))
                    <div class="mt-4">
                        {!! $csrfField !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
