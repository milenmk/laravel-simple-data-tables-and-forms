@props(['item', 'column'])

@php
    $field = $column->key;
    $value = (bool) $item->{$field};
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
    <div
            @class(['flex items-center', 'justify-start' => $column->align === 'left', 'justify-end' => $column->align === 'right', 'justify-center' => $column->align === 'center'])
    >
        <input
                type="checkbox"
                wire:click="toggleValue('{{ $item->id }}', '{{ $field }}')"
                id="switch_{{ $item->id }}_{{ $field }}"
                {{ $value ? 'checked' : '' }}
        />
    </div>
</td>
