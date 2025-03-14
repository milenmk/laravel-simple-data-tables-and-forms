@php
    $value = (bool) $column->getValue($item);
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
    <livewire:components.toggle-switch
            :model="$item"
            field="{{ $column->key }}"
            :key="'toggle-'.$item->id"
            align="{{ $column->align }}"
            textColor="{{ $column->color }}"
    />
</td>
