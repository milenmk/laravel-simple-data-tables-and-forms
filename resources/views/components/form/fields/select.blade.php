@php
    $baseAttributes = [
        "class" => "form-field-wrapper " . ($errors->has("formData" . $field->name) ? "has-error" : ""),
    ];
    $mergedAttributes = $field->getMergedAttributes($baseAttributes);

    // Convert array to HTML attributes string
    $attributesString = "";
    foreach ($mergedAttributes as $key => $value) {
        $attributesString .= $key . '="' . e($value) . '" ';
    }
@endphp

@unless ($field->isHidden())
    <div {!! $attributesString !!}>
        <label for="{{ $field->name }}" class="form-label">
            {{ $field->getLabel() }}
            @if ($field->required)
                <span class="text-danger">*</span>
            @endif
        </label>

        @if ($field->searchable)
            {{-- Searchable Select Component --}}
            <div
                x-data="searchableSelect({
                            options: @js(collect($field->getOptions())->map(fn ($label, $value) => ["value" => $value, "label" => $label])->values()),
                            value: @entangle("formData." . $field->name).defer,
                            multiple: {{ $field->multiple ? "true" : "false" }},
                            placeholder:
                                '{{ $field->getPlaceholder() ?: ($field->emptyOption ?: "Select an option...") }}',
                            searchPlaceholder: 'Search options...',
                        })"
                class="relative"
                @click.away="isOpen = false"
            >
                {{-- Hidden input for form submission --}}
                <input
                    type="hidden"
                    name="{{ $field->name }}{{ $field->multiple ? "[]" : "" }}"
                    :value="multiple ? JSON.stringify(value) : value"
                    @if ($field->required) required @endif
                />

                {{-- Trigger Button --}}
                <button
                    type="button"
                    @click="toggle()"
                    :disabled="{{ $field->isDisabled() ? "true" : "false" }}"
                    class="searchable-select-trigger {{ $field->multiple ? "form-multiselect" : "form-select" }} {{ $field->isDisabled() ? "cursor-not-allowed opacity-50" : "" }} flex w-full items-center justify-between text-left"
                    :class="{ 'text-gray-400': selectedOptions.length === 0 }"
                    @foreach ($field->attributes as $key => $value)
                        {{ $key }}="{{ $value }}"
                    @endforeach
                >
                    <div class="flex min-w-0 flex-1 flex-wrap gap-1">
                        <template x-if="!multiple && selectedOptions.length === 0">
                            <span class="text-gray-400" x-text="placeholder"></span>
                        </template>

                        <template x-if="! multiple && selectedOptions.length > 0">
                            <span x-text="selectedOptions[0].label"></span>
                        </template>

                        <template x-if="multiple && selectedOptions.length === 0">
                            <span class="text-gray-400" x-text="placeholder"></span>
                        </template>

                        <template x-if="multiple && selectedOptions.length > 0">
                            <div class="flex flex-wrap gap-1">
                                <template x-for="option in selectedOptions" :key="option.value">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-md bg-blue-100 px-2 py-1 text-xs text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                                    >
                                        <span x-text="option.label"></span>
                                        <button
                                            type="button"
                                            @click.stop="removeOption(option)"
                                            class="hover:text-blue-600 dark:hover:text-blue-300"
                                        >
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                ></path>
                                            </svg>
                                        </button>
                                    </span>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="ml-2 flex items-center gap-2">
                        <template
                            x-if="selectedOptions.length > 0 && ! {{ $field->isDisabled() ? "true" : "false" }}"
                        >
                            <button
                                type="button"
                                @click.stop="clear()"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            >
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                            </button>
                        </template>

                        <svg
                            class="h-4 w-4 text-gray-400 transition-transform"
                            :class="{ 'rotate-180': isOpen }"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>
                    </div>
                </button>

                {{-- Dropdown --}}
                <div
                    x-show="isOpen"
                    x-transition:enter="transition duration-100 ease-out"
                    x-transition:enter-start="scale-95 transform opacity-0"
                    x-transition:enter-end="scale-100 transform opacity-100"
                    x-transition:leave="transition duration-75 ease-in"
                    x-transition:leave-start="scale-100 transform opacity-100"
                    x-transition:leave-end="scale-95 transform opacity-0"
                    class="absolute z-50 mt-1 w-full rounded-md border border-gray-300 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-800"
                    x-ref="dropdown"
                >
                    {{-- Search Input --}}
                    <div class="border-b border-gray-200 p-2 dark:border-gray-600">
                        <input
                            type="text"
                            x-model="searchQuery"
                            x-ref="searchInput"
                            :placeholder="searchPlaceholder"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            @keydown.enter.prevent
                        />
                    </div>

                    {{-- Options List --}}
                    <div class="max-h-60 overflow-y-auto">
                        <template x-if="filteredOptions.length === 0">
                            <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400" x-text="emptyText"></div>
                        </template>

                        <template x-for="(option, index) in filteredOptions" :key="option.value">
                            <button
                                type="button"
                                @click="selectOption(option)"
                                :data-highlighted="highlightedIndex === index"
                                class="flex w-full items-center justify-between px-3 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700"
                                :class="{
                                'bg-blue-50 dark:bg-blue-900/20': highlightedIndex === index,
                                'bg-blue-100 dark:bg-blue-900/40': isSelected(option)
                            }"
                            >
                                <span x-text="option.label"></span>
                                <template x-if="isSelected(option)">
                                    <svg
                                        class="h-4 w-4 text-blue-600 dark:text-blue-400"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </template>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        @else
            {{-- Regular Select --}}
            <select
                id="{{ $field->name }}"
                name="{{ $field->name }}{{ $field->multiple ? "[]" : "" }}"
                @if ($field->reactive)
                    wire:model.live="formData.{{ $field->name }}"
                @else
                    wire:model.defer="formData.{{ $field->name }}"
                @endif
                class="{{ $field->multiple ? "form-multiselect" : "form-select" }} {{ $field->isDisabled() ? "cursor-not-allowed opacity-50" : "" }}"
                @if ($field->required) required @endif
                @if ($field->isDisabled()) disabled @endif
                @if ($field->multiple)
                    multiple
                @endif
                @foreach ($field->attributes as $key => $value)
                    {{ $key }}="{{ $value }}"
                @endforeach
            >
                @if ($field->emptyOption && ! $field->multiple)
                    <option value="">{{ $field->emptyOption }}</option>
                @endif

                @foreach ($field->getOptions() as $value => $label)
                    <option value="{{ $value }}">
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        @endif

        @if ($field->helperText)
            <p class="form-help dark:text-white-dark mt-1 text-sm text-gray-600">{{ $field->helperText }}</p>
        @endif

        @error("formData." . $field->name)
            <p class="form-help text-danger mt-1 text-sm">{{ $message }}</p>
        @enderror
    </div>
@endunless
