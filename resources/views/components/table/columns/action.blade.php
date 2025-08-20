@php
    $actions = $column->getActions();
@endphp

<td
    id="td-{{ $column->key }}"
    data-column="{{ $column->key }}"
    @class([
        'items-center px-2 py-1.5',
        $column->textColor => $column->textColor ?: 'text-gray-500',
        $column->backgroundColor => $column->backgroundColor ?: '',
        'text-wrap' => $column->wrap,
        'text-nowrap' => ! $column->wrap,
        match ($column->weight) {
            'thin' => 'font-thin',
            'extralight' => 'font-extralight',
            'light' => 'font-light',
            'medium' => 'font-medium',
            'semibold' => 'font-semibold',
            'bold' => 'font-bold',
            'extrabold' => 'font-extrabold',
            'black' => 'font-black',
            default => 'font-normal',
        },
    ])
    style="text-align: {{ $column->align }}"
>
    @if (! $column->groupActions)
        <div class="flex items-center justify-center space-x-2">
            @foreach ($actions as $action)
                @include($action->getView())
            @endforeach
        </div>
    @else
        <ul class="horizontal-menu dark:text-white-dark text-black">
            <li class="nav-item relative">
                <a href="javascript:" class="nav-link">
                    <svg
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 rotate-90 opacity-70 hover:opacity-100"
                    >
                        <circle cx="5" cy="12" r="2" stroke="currentColor" stroke-width="1.5"></circle>
                        <circle opacity="0.5" cx="12" cy="12" r="2" stroke="currentColor" stroke-width="1.5"></circle>
                        <circle cx="19" cy="12" r="2" stroke="currentColor" stroke-width="1.5"></circle>
                    </svg>
                </a>
                <ul class="sub-menu w-30 px-2 py-2 font-semibold ltr:right-0 rtl:left-0">
                    @foreach ($actions as $action)
                        <li class="my-2">
                            @include($action->getView())
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>
    @endif
</td>
