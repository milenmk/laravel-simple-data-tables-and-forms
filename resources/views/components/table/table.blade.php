@props([
    "heading" => null,
    "columns" => null,
    "data" => null,
    "striped" => false,
    "groups" => null,
    "selectedGroup" => null,
    "collapsedGroups" => [],
    "showFilters" => false,
    "tableFilters" => [],
    "filters" => [],
    ])

<div class="panel">
    @if ($heading)
        <h5 class="dark:text-white-light mb-5 text-lg font-semibold md:absolute md:top-[25px] md:mb-0">
            {{ $heading }}
        </h5>
    @endif

    <div class="relative">
        <div class="mb-5 sm:absolute sm:top-0 sm:mb-0">

        </div>

        <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
            <div class="dataTable-top">
                <div class="flex items-center">
                    <div
                            class="theme-dropdown relative"
                            x-data="{ columnDropdown: false }"
                            @click.outside="columnDropdown = false"
                    >
                        <a
                                href="javascript:"
                                class="dark:text-white-dark flex items-center rounded-md border border-[#e0e6ed] px-4 py-2 text-sm font-semibold dark:border-[#253b5c] dark:bg-[#1b2e4b]"
                                @click="columnDropdown = ! columnDropdown"
                        >
                            <span class="ltr:mr-1 rtl:ml-1">Columns</span>
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
                                class="text-dark dark:text-white-light absolute top-11 z-[10] hidden w-[100px] min-w-[150px] rounded bg-white py-2 shadow ltr:left-0 rtl:right-0 dark:bg-[#1b2e4b]"
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
                                                        wire:click="toggleColumnVisibility('{{ $column->key }}')"
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
                </div>
                <div>
                    <select wire:model.live="selectedGroup" class="form-select" key="grouping-select">
                        <option value="">{{ __('No Grouping') }}</option>
                        @if (isset($groups))
                            @foreach ($groups as $group)
                                <option value="{{ $group->key }}">{{ __($group->label) }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="dataTable-search">
                    <div x-data="{ search: @entangle("search").defer || '' }" class="relative w-full items-center">
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
                                @input.debounce.300ms="$wire.set('search', search)"
                                class="dataTable-input ease w-full rounded-md border border-slate-200 bg-transparent py-2 pr-10 pl-10 text-sm text-slate-700 shadow-sm transition duration-300 placeholder:text-slate-400 hover:border-slate-300 focus:border-slate-400 focus:shadow focus:outline-none"
                                placeholder="Search..."
                                type="text"
                                style="padding-left: 2rem"
                        />
                        <button
                                type="button"
                                @click="search = ''; $wire.set('search', '')"
                                x-show="search.length > 0"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-xl text-gray-500 hover:text-gray-700 focus:outline-none"
                        >
                            &times;
                        </button>
                    </div>
                </div>

                <!-- Filter Button -->
                <button
                        type="button"
                        wire:click="$toggle('showFilters')"
                        class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    {{ __('Filters') }} ({{ $this->getAppliedFiltersCount() }})
                </button>
            </div>

            <div class="dataTable-top">
                <!-- Filters Panel -->
                @if ($showFilters)
                    <div class="w-full">
                        <div class="mt-4 p-4 border rounded-md bg-gray-50">
                            <div class="grid grid-cols-6 gap-4"> <!-- Add grid container -->
                                @foreach ($tableFilters as $filter)
                                    {!! $filter['view'] !!}
                                @endforeach
                            </div>
                            <div class="flex justify-end mt-4">
                                <!-- Reset Button -->
                                <button
                                        type="button"
                                        wire:click="resetFilters"
                                        class="mt-2 inline-flex items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                >
                                    {{ __('Reset Filters') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Active Filters Display -->
            @if (!empty($filters))
                <div class="flex flex-wrap items-center mb-4">
                    @foreach ($filters as $filterName => $filterValues)
                        @if (!empty($filterValues))
                            @php
                                $filterItem = collect($tableFilters)->firstWhere('name', $filterName);
                                $filterLabel = $filterItem['filterLabel'] ?? $filterName;
                                $filterOptions = $filterItem['filterOptions'] ?? [];
                            @endphp

                            @if (is_array($filterValues))
                                @foreach ($filterValues as $filterValue)
                                    <div class="inline-flex items-center mr-2 mb-2 px-3 py-1 rounded-full text-sm font-medium bg-gray-200 dark:bg-gray-700">
                                            <span class="mr-2">
                                                {{ $filterLabel }}: {{ $filterOptions[$filterValue] ?? $filterValue }}
                                            </span>
                                        <button
                                                wire:click="removeFilter('{{ $filterName }}', '{{ $filterValue }}')"
                                                type="button"
                                                class="text-gray-500 hover:text-gray-700 focus:outline-none"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                @php
                                    $boolValue = ($filterValues === true) ? 'Yes' : (($filterValues === false) ? 'No' : null);
                                    $displayValue = $boolValue ?? $filterValues;
                                @endphp
                                <div class="inline-flex items-center mr-2 mb-2 px-3 py-1 rounded-full text-sm font-medium bg-gray-200 dark:bg-gray-700">
                                        <span class="mr-2">
                                            {{ $filterLabel }}: {{ $displayValue }}
                                        </span>
                                    <button
                                            wire:click="removeFilter('{{ $filterName }}', '{{ $filterValues }}')"
                                            type="button"
                                            class="text-gray-500 hover:text-gray-700 focus:outline-none"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>
            @endif

            <div class="dataTable-container">
                <table
                        id="myTable"
                        @class([
                            "dataTable-table min-w-full divide-y divide-gray-200",
                            "table-striped" => $striped,
                        ])
                >
                    <thead class="bg-gray-50">
                    <tr>
                        @foreach ($columns as $column)
                            @if ($column->visible)
                                <th
                                        id="th-{{ $column->key }}"
                                        @class([
                                            "items-center px-6 py-3 font-medium tracking-wider text-gray-500 uppercase",
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
                                    <button wire:click="toggleGroupVisibility('{{ $groupKey }}')" type="button">
                                        {{ __($groupCollection->firstWhere('key', $selectedGroup)->label ?? '') }}
                                        : @if ($groupKey === 1)
                                            {{ __('Yes') }}
                                        @elseif ($groupKey === 0)
                                            {{ __('No') }}
                                        @else
                                            {{ __($groupKey) }}
                                        @endif
                                        @if (in_array($groupKey, $collapsedGroups))
                                            <span>&#9654;</span> <!-- Right arrow when collapsed -->
                                        @else
                                            <span>&#9660;</span> <!-- Down arrow when expanded -->
                                        @endif
                                    </button>
                                </td>
                            </tr>
                            @if (!in_array($groupKey, $collapsedGroups))
                                @foreach ($items as $item)
                                    <tr wire:key="{{ $item->id }}">
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
                            <tr wire:key="{{ $item->id }}">
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
                                        <img src="{{ asset("assets/images/empty.png") }}" alt="{{ __("noData") }}"/>
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

                <div class="dataTable-bottom mt-4">
                    {{ $data->links("pagination::tailwind") }}
                </div>
            </div>
        </div>
    </div>
</div>
