<div class="form-field-wrapper {{ $errors->has('formData.' . $field->name) ? 'has-error' : '' }}">
    <label for="{{ $field->name }}" class="form-label {{ $field->required ? 'required' : '' }}">
        {{ $field->getLabel() }}
    </label>

    <div class="form-input-group">
        {{-- Prepend Addon --}}
        @if ($field->prependText || $field->prependIcon)
            <div class="form-input-addon">
                @if ($field->prependIcon)
                    @if (is_string($field->prependIcon))
                        <x-dynamic-component :component="'heroicon-o-' . $field->prependIcon" class="h-4 w-4" />
                    @else
                        {!! $field->prependIcon !!}
                    @endif
                @endif

                @if ($field->prependText)
                    <span>{{ $field->prependText }}</span>
                @endif
            </div>
        @endif

        {{-- Input Field --}}
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

        {{-- Append Addon --}}
        @if ($field->appendText || $field->appendIcon)
            <div class="form-input-addon">
                @if ($field->appendIcon)
                    @if (is_string($field->appendIcon))
                        <x-dynamic-component :component="'heroicon-o-' . $field->appendIcon" class="h-4 w-4" />
                    @else
                        {!! $field->appendIcon !!}
                    @endif
                @endif

                @if ($field->appendText)
                    <span>{{ $field->appendText }}</span>
                @endif
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
