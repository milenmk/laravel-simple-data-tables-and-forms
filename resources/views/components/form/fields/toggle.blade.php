<div class="form-field-wrapper {{ $errors->has('formData.' . $field->name) ? 'has-error' : '' }}">
    <div class="flex items-center justify-between">
        <div class="flex flex-col">
            <label for="{{ $field->name }}" class="form-label">
                {{ $field->getLabel() }}
                @if ($field->required)
                    <span class="text-danger">*</span>
                @endif
            </label>
            @if ($field->helperText)
                <p class="form-help dark:text-white-dark mt-1 text-sm text-gray-600">{{ $field->helperText }}</p>
            @endif
        </div>

        <div class="toggle-wrapper">
            <label class="toggle-switch {{ $field->size }}">
                <input
                    type="checkbox"
                    id="{{ $field->name }}"
                    name="{{ $field->name }}"
                    wire:model.defer="formData.{{ $field->name }}"
                    value="{{ $field->onValue }}"
                    class="toggle-input"
                    @if ($field->required) required @endif
                    @if ($field->disabled) disabled @endif
                    @foreach ($field->attributes as $key => $value)
                        {{ $key }}="{{ $value }}"
                    @endforeach
                />
                <span class="toggle-slider {{ $field->color }}"></span>
            </label>

            @if ($field->onLabel || $field->offLabel)
                <div class="toggle-labels">
                    @if ($field->offLabel)
                        <span class="off-label">{{ $field->offLabel }}</span>
                    @endif

                    @if ($field->onLabel)
                        <span class="on-label">{{ $field->onLabel }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @error("formData." . $field->name)
        <p class="form-help text-danger mt-1 text-sm">{{ $message }}</p>
    @enderror

    <input type="hidden" name="{{ $field->name }}" value="{{ $field->offValue }}" />
</div>
