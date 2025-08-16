<div class="form-field-wrapper {{ $errors->has('formData.' . $field->name) ? 'has-error' : '' }}">
    @if ($field->type !== "hidden")
        <label for="{{ $field->name }}" class="form-label {{ $field->required ? 'required' : '' }}">
            {{ $field->getLabel() }}
        </label>
    @endif

    <div
        class="form-field-with-icon {{ $field->leftIcon ? 'has-left-icon' : '' }} {{ $field->rightIcon ? 'has-right-icon' : '' }}"
    >
        {{-- Left Icon --}}
        @if ($field->leftIcon)
            <div class="form-field-icon left">
                @if (is_string($field->leftIcon))
                    <x-dynamic-component :component="'heroicon-o-' . $field->leftIcon" class="h-5 w-5" />
                @else
                    {!! $field->leftIcon !!}
                @endif
            </div>
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
            @if ($field->readonly) readonly @endif
            @if ($field->minLength) minlength="{{ $field->minLength }}" @endif
            @if ($field->maxLength) maxlength="{{ $field->maxLength }}" @endif
            @if ($field->pattern) pattern="{{ $field->pattern }}" @endif
            @if ($field->min !== null) min="{{ $field->min }}" @endif
            @if ($field->max !== null) max="{{ $field->max }}" @endif
            @if ($field->step) step="{{ $field->step }}" @endif
            @foreach ($field->attributes as $key => $value)
                {{ $key }}="{{ $value }}"
            @endforeach
            value="{{ old($field->name, $field->default ?? '') }}"
        />

        {{-- Right Icon --}}
        @if ($field->rightIcon)
            <div class="form-field-icon right">
                @if (is_string($field->rightIcon))
                    <x-dynamic-component :component="'heroicon-o-' . $field->rightIcon" class="h-5 w-5" />
                @else
                    {!! $field->rightIcon !!}
                @endif
            </div>
        @endif

        {{-- Loading Indicator --}}
        @if ($field->loading)
            <div class="form-field-icon right">
                <svg
                    class="text-primary h-5 w-5 animate-spin"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                </svg>
            </div>
        @endif
    </div>

    @if ($field->helperText)
        <p class="form-help">{{ $field->helperText }}</p>
    @endif

    @error("formData." . $field->name)
        <p class="form-help text-danger">{{ $message }}</p>
    @enderror
</div>
