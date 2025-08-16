<input
    type="hidden"
    id="{{ $field->name }}"
    name="{{ $field->name }}"
    wire:model.defer="formData.{{ $field->name }}"
    @foreach ($field->attributes as $key => $value)
        {{ $key }}="{{ $value }}"
    @endforeach
/>
