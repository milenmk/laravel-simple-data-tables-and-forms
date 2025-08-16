<div class="form-container {{ $theme }}">
    {{-- Include CSRF protection --}}
    {!! $csrfField ?? '' !!}

    @if ($heading)
        <div class="form-heading mb-6">
            @if (is_array($heading))
                <h2 class="text-2xl font-bold text-gray-900">{{ $heading['title'] ?? '' }}</h2>
                @if (isset($heading['description']))
                    <p class="mt-2 text-gray-600">{{ $heading['description'] }}</p>
                @endif
            @else
                <h2 class="text-2xl font-bold text-gray-900">{{ $heading }}</h2>
            @endif
        </div>
    @endif

    @if (count($sections) > 0)
        @foreach ($sections as $section)
            @php
                $baseAttributes = [
                    'class' => 'form-section mb-8',
                ];
                $mergedAttributes = $section->getMergedAttributes($baseAttributes);

                // Convert array to HTML attributes string
                $attributesString = '';
                foreach ($mergedAttributes as $key => $value) {
                    $attributesString .= $key . '="' . e($value) . '" ';
                }
            @endphp

            <div
                {!! $attributesString !!}
                @if ($section->collapsible) x-data="{ isOpen: {{ $section->collapsed ? 'false' : 'true' }} }" @endif
            >
                @if ($section->label || $section->description)
                    <div class="section-header mb-4">
                        @if ($section->collapsible)
                            <div class="flex cursor-pointer items-center justify-between" @click="isOpen = !isOpen">
                                <div class="flex items-center">
                                    @if ($section->icon)
                                        <x-laravel-simple-datatables-and-forms::icon
                                            :icon="$section->icon"
                                            class="mr-2 h-5 w-5 text-gray-500"
                                        />
                                    @endif

                                    <h3 class="text-lg font-semibold text-gray-900">{{ $section->getLabel() }}</h3>
                                </div>
                                <div
                                    class="transition-transform duration-300"
                                    :class="isOpen ? 'rotate-180' : 'rotate-0'"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="m19.5 8.25-7.5 7.5-7.5-7.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center">
                                @if ($section->icon)
                                    <x-laravel-simple-datatables-and-forms::icon
                                        :icon="$section->icon"
                                        class="mr-2 h-5 w-5 text-gray-500"
                                    />
                                @endif

                                <h3 class="text-lg font-semibold text-gray-900">{{ $section->getLabel() }}</h3>
                            </div>
                        @endif
                        @if ($section->description)
                            <p class="mt-1 text-sm text-gray-600">{{ $section->description }}</p>
                        @endif
                    </div>
                @endif

                @if ($section->collapsible)
                    <div
                        x-show="isOpen"
                        x-transition:enter="transition duration-300 ease-out"
                        x-transition:enter-start="-translate-y-4 transform opacity-0"
                        x-transition:enter-end="translate-y-0 transform opacity-100"
                        x-transition:leave="transition duration-300 ease-in"
                        x-transition:leave-start="translate-y-0 transform opacity-100"
                        x-transition:leave-end="-translate-y-4 transform opacity-0"
                        class="mt-4"
                    >
                        <div class="grid-cols-{{ $section->columns }} grid gap-4">
                            @foreach ($section->fields as $field)
                                <div
                                    class="form-field {{ $field->columnSpan ? 'col-span-' . $field->columnSpan : '' }}"
                                >
                                    {!! $field->render() !!}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="grid-cols-{{ $section->columns }} grid gap-4">
                        @foreach ($section->fields as $field)
                            <div class="form-field {{ $field->columnSpan ? 'col-span-' . $field->columnSpan : '' }}">
                                {!! $field->render() !!}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="grid-cols-{{ $columns }} grid gap-4">
            @foreach ($fields as $field)
                <div class="form-field {{ $field->columnSpan ? 'col-span-' . $field->columnSpan : '' }}">
                    {!! $field->render() !!}
                </div>
            @endforeach
        </div>
    @endif
</div>
