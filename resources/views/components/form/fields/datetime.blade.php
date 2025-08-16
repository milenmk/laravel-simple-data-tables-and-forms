<div class="form-field-wrapper {{ $errors->has('formData.' . $field->name) ? 'has-error' : '' }}">
    @if ($field->type !== "hidden")
        <label for="{{ $field->name }}" class="form-label">
            {{ $field->getLabel() }}
            @if ($field->required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $field->type }}"
        id="{{ $field->name }}"
        name="{{ $field->name }}"
        wire:model.defer="formData.{{ $field->name }}"
        class="form-input {{ $field->disabled ? 'cursor-not-allowed opacity-50' : '' }}"
        @if ($field->placeholder) placeholder="{{ $field->placeholder }}" @endif
        @if ($field->required) required @endif
        @if ($field->disabled) disabled @endif
        @if ($field->min) min="{{ $field->min }}" @endif
        @if ($field->max) max="{{ $field->max }}" @endif
        @if ($field->step) step="{{ $field->step }}" @endif
        @foreach ($field->attributes as $key => $value)
            {{ $key }}="{{ $value }}"
        @endforeach
    />

    @if ($field->helperText)
        <p class="form-help dark:text-white-dark mt-1 text-sm text-gray-600">{{ $field->helperText }}</p>
    @endif

    @error("formData." . $field->name)
        <p class="form-help text-danger mt-1 text-sm">{{ $message }}</p>
    @enderror
</div>
