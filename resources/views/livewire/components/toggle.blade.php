@props(['textColor' => '', 'align' => ''])

<div
    @class(['flex items-center', 'justify-start' => $align === 'left', 'justify-end' => $align === 'right', 'justify-center' => $align === 'center'])
>
    <label class="relative h-6 w-12">
        <input
            type="checkbox"
            wire:model.live="value"
            class="custom_switch peer absolute z-10 h-full w-full cursor-pointer opacity-0"
            id="switch_{{ $model->id }}_{{ $field }}"
        />
        <span
            for="switch_{{ $model->id }}_{{ $field }}"
            @class(['dark:bg-dark dark:before:bg-white-dark block h-full rounded-full bg-[#ebedf2] before:absolute before:bottom-1 before:left-1 before:h-4 before:w-4 before:rounded-full before:bg-white before:transition-all before:duration-300 peer-checked:before:left-7 dark:peer-checked:before:bg-white',match ($textColor) {'secondary' => 'peer-checked:bg-secondary','success' => 'peer-checked:bg-success','danger' => 'peer-checked:bg-danger','warning' => 'peer-checked:bg-warning','info' => 'peer-checked:bg-info',default => 'peer-checked:bg-primary',},])
        ></span>
    </label>
</div>
