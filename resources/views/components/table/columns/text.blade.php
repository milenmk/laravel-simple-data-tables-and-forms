@php
    $value = $column->getValue($item);
@endphp

<td
        id="td-{{ $column->key }}"
        data-column="{{ $column->key }}"
        @class([
            'items-center px-4 py-2',
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
    @if ($column->isDate)
        {{ $value->format('Y-m-d') }}
    @elseif ($column->isNumeric)
        {{ number_format($value, $column->decimalPlaces) }}
    @elseif ($column->isBadge)
        <span
            @class([
                'badge',
                match ($column->color) {
                    'secondary' => 'bg-secondary',
                    'success' => 'bg-success',
                    'danger' => 'bg-danger',
                    'warning' => 'bg-warning',
                    'info' => 'bg-info',
                    default => 'bg-primary',
                },
            ])
        >
            {{ $value }}
        </span>
    @else
        <span>
            {{ $value }}
        </span>
    @endif

    @if ($column->description)
        <div>
            <span class="text-sm">{{ $column->getDescription($item) }}</span>
        </div>
    @endif
</td>
