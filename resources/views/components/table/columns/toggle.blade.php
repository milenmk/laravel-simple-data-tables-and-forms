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
    <label class="relative h-6 w-12">
        <input
            type="checkbox"
            wire:model="{{ $column->wireModel }}"
            wire:model.attr="disabled"
            {{ $value ? 'checked' : '' }}
            class="custom_switch peer absolute z-10 h-full w-full cursor-pointer opacity-0"
            id="switch_{{ $column->key }}"
        />
        <span
            for="switch_{{ $column->key }}"
            @class(['dark:bg-dark dark:before:bg-white-dark block h-full rounded-full bg-[#ebedf2] before:absolute before:bottom-1 before:left-1 before:h-4 before:w-4 before:rounded-full before:bg-white before:transition-all before:duration-300 peer-checked:before:left-7 dark:peer-checked:before:bg-white',match ($column->color) {'secondary' => 'peer-checked:bg-secondary','success' => 'peer-checked:bg-success','danger' => 'peer-checked:bg-danger','warning' => 'peer-checked:bg-warning','info' => 'peer-checked:bg-info',default => 'peer-checked:bg-primary',},])
        ></span>
    </label>
</td>
