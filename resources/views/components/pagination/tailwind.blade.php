@php
    if (! isset($scrollTo)) {
        $scrollTo = 'body';
    }

    $scrollIntoViewJsSnippet = ($scrollTo !== false)
        ? <<<JS
           (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
        JS
        : '';
@endphp
@if ($paginator->hasPages())
    <div class="dataTable-info">
        <span>{!! __('Showing') !!}</span>
        <span class="font-medium">{{ $paginator->firstItem() }}</span>
        <span>{!! __('to') !!}</span>
        <span class="font-medium">{{ $paginator->lastItem() }}</span>
        <span>{!! __('of') !!}</span>
        <span class="font-medium">{{ $paginator->total() }}</span>
        <span>{!! __('results') !!}</span>
    </div>

    <div class="dataTable-dropdown">
        <label>
            <select wire:model.live="perPage" class="dataTable-selector">
                @php
                    $perPageArray = [5, 10, 20, 50];
                @endphp

                @foreach ($perPageArray as $option)
                    @if ($option == $this->perPage)
                        <option value="{{ $option }}" selected>{{ $option }}</option>
                    @else
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endif
                @endforeach
            </select>
            {{ __('perPage') }}
        </label>
    </div>

    <nav class="dataTable-pagination">
        <ul class="dataTable-pagination-list">
            @if (!$paginator->onFirstPage())
                <li class="pager">
                    <button
                        type="button"
                        wire:click="previousPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled"
                        dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before"
                    >
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                             class="w-4.5 h-4.5 rtl:rotate-180">
                            <path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                  stroke-linejoin="round">
                            </path>
                        </svg>
                    </button>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li aria-disabled="true">
                        <button>
                            {{ $element }}
                        </button>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active ml-1">
                                <button type="button">
                                    {{ $page }}
                                </button>
                            </li>
                        @else
                            <li class="ml-1">
                                <button
                                    type="button"
                                    wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                >
                                    {{ $page }}
                                </button>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="pager ml-1">
                    <button
                        type="button"
                        wire:click="nextPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled"
                        dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before"
                    >
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                             class="w-4.5 h-4.5 rtl:rotate-180">
                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                  stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </li>
            @endif
        </ul>
    </nav>
@else
    <div class="dataTable-dropdown">
        <label>
            <select wire:model.live="perPage" class="dataTable-selector">
                @php
                    $perPageArray = [5, 10, 20, 50];
                @endphp

                @foreach ($perPageArray as $option)
                    @if ($option == $this->perPage)
                        <option value="{{ $option }}" selected>{{ $option }}</option>
                    @else
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endif
                @endforeach
            </select>
            {{ __('perPage') }}
        </label>
    </div>
@endif
