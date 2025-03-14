@props(['align' => ''])

<div
    @class(['flex items-center', 'justify-start' => $align === 'left', 'justify-end' => $align === 'right', 'justify-center' => $align === 'center'])
>
    <input
        type="checkbox"
        wire:model.live="value"
        id="switch_{{ $model->id }}_{{ $field }}"
        {{ $value ? 'checked' : '' }}
    />
</div>
