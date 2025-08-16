<div class="form-field-wrapper {{ $errors->has('formData.' . $field->name) ? 'has-error' : '' }}">
    <label class="form-label {{ $field->required ? 'required' : '' }}">
        {{ $field->getLabel() }}
    </label>

    <div class="form-radio-group {{ $field->inline ? 'inline' : '' }}">
        @foreach ($field->getOptions() as $value => $label)
            <div class="form-radio-item">
                <input
                    type="radio"
                    id="{{ $field->name }}_{{ $loop->index }}"
                    name="{{ $field->name }}"
                    wire:model.defer="formData.{{ $field->name }}"
                    class="form-radio {{ $field->color ? 'outline-' . $field->color : '' }} {{ $field->disabled ? 'cursor-not-allowed opacity-50' : '' }}"
                    value="{{ $value }}"
                    @if ($field->required) required @endif
                    @if ($field->disabled) disabled @endif
                    @foreach ($field->attributes as $key => $attr_value)
                        {{ $key }}="{{ $attr_value }}"
                    @endforeach
                />
                <label for="{{ $field->name }}_{{ $loop->index }}" class="ml-2 cursor-pointer text-sm font-medium">
                    {{ $label }}
                </label>
            </div>
        @endforeach
    </div>

    @if ($field->helperText)
        <p class="form-help">{{ $field->helperText }}</p>
    @endif

    @error("formData." . $field->name)
        <p class="form-help text-danger">{{ $message }}</p>
    @enderror
</div>
