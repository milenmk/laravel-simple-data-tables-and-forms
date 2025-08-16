<div
    class="form-section {{ $section->compact ? 'compact' : '' }} {{ $section->aside ? 'aside' : '' }} {{ $section->collapsible ? 'collapsible' : '' }}"
    x-data="{ collapsed: {{ $section->collapsed ? 'true' : 'false' }} }"
    @if (!$section->visible) style="display: none;" @endif
>
    <div
        class="form-section-header {{ $section->collapsible ? 'cursor-pointer' : '' }}"
        @if ($section->collapsible) @click="collapsed = !collapsed" @endif
    >
        <div class="flex items-center">
            @if ($section->icon)
                <div class="dark:text-white-dark mr-3 text-gray-500">
                    <x-dynamic-component :component="'heroicon-o-' . $section->icon" class="h-5 w-5" />
                </div>
            @endif

            <div>
                <h3 class="form-section-title">{{ $section->getLabel() }}</h3>
                @if ($section->description)
                    <p class="form-section-description">{{ $section->description }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center space-x-2">
            {{-- Header Actions --}}
            @if (! empty($section->headerActions))
                @foreach ($section->headerActions as $action)
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-{{ $action->color ?? 'primary' }}"
                        @if ($action->action) wire:click="{{ $action->action }}" @endif
                        @if ($action->url) onclick="window.location.href='{{ $action->url }}'" @endif
                        @if ($action->disabled) disabled @endif
                        @if (!$action->visible) style="display: none;" @endif
                    >
                        @if ($action->icon)
                            <x-dynamic-component :component="'heroicon-o-' . $action->icon" class="mr-1 h-4 w-4" />
                        @endif

                        {{ $action->getLabel() }}
                    </button>
                @endforeach
            @endif

            {{-- Collapse Toggle --}}
            @if ($section->collapsible)
                <div
                    class="dark:text-white-dark text-gray-500 transition-colors hover:text-gray-700 dark:hover:text-white"
                >
                    <svg x-show="!collapsed" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <svg x-show="collapsed" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
            @endif
        </div>
    </div>

    <div
        class="form-section-content"
        x-show="! collapsed || ! {{ $section->collapsible ? 'true' : 'false' }}"
        x-collapse
    >
        @if ($section->view)
            {{-- Custom view --}}
            @include($section->view)
        @elseif ($section->content)
            {{-- Custom content --}}
            {!! $section->content !!}
        @else
            {{-- Render fields --}}
            <div
                class="form-grid cols-{{ is_array($section->columns) ? implode(' ', $section->columns) : $section->columns }}"
            >
                @foreach ($section->schema as $field)
                    <div
                        class="{{ isset($field->columnSpan) && $field->columnSpan !== 'auto' ? 'field-col-span-' . $field->columnSpan : '' }}"
                    >
                        {{ $field->render() }}
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
