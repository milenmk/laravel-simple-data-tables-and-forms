@php
    $progressValue = $column->getValue($item);

    // Determine color based on value or use custom color
    if ($progressValue <= 100) {
        $color = $column->color ?: 'bg-primary'; // Use the custom color set in ProgressColumn
        $fill = $progressValue;
    } else {
        $color = $column->color ?: 'bg-danger'; // Fallback for values over 100
        $fill = 100;
    }
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
    @if ($column->description)
        <span class="ml-4 text-sm">{{ $column->description }}</span>
    @endif

    <div class="mr-4 ml-4 h-4 w-full overflow-hidden rounded-md bg-gray-200 shadow-inner dark:bg-gray-800">
        <div
            class="{{ $color }} dark:bg-opacity-50 {{ $column->textColor ?: 'text-white' }} flex h-full items-center justify-start rounded-md text-xs font-semibold shadow-inner"
            style="width: {{ $fill }}%"
        >
            <span class="pl-1">{{ number_format($progressValue, 2) }}%</span>
        </div>
    </div>
</td>
