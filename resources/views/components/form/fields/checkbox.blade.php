<div class="form-field-wrapper {{ $errors->has('formData.' . $field->name) ? 'has-error' : '' }}">
    <div class="{{ $field->inline ? 'flex-row' : 'flex-col' }} flex items-start">
        <div class="{{ $field->inline ? 'mr-3' : 'mb-2' }} flex items-center">
            <input
                type="checkbox"
                id="{{ $field->name }}"
                name="{{ $field->name }}"
                wire:model.defer="formData.{{ $field->name }}"
                class="form-checkbox {{ $field->disabled ? 'cursor-not-allowed opacity-50' : '' }}"
                value="{{ $field->checkedValue }}"
                @if ($field->required) required @endif
                @if ($field->disabled) disabled @endif
                @foreach ($field->attributes as $key => $value)
                    {{ $key }}="{{ $value }}"
                @endforeach
            />
            <label for="{{ $field->name }}" class="form-label ml-2 text-sm font-medium">
                {{ $field->getLabel() }}
                @if ($field->required)
                    <span class="text-danger">*</span>
                @endif
            </label>
        </div>
    </div>

    @if ($field->helperText)
        <p class="form-help dark:text-white-dark mt-1 text-sm text-gray-600">{{ $field->helperText }}</p>
    @endif

    @error("formData." . $field->name)
        <p class="form-help text-danger mt-1 text-sm">{{ $message }}</p>
    @enderror

    <input type="hidden" name="{{ $field->name }}" value="{{ $field->uncheckedValue }}" />
</div>
