@php
    $baseAttributes = [
        "class" => "form-field-wrapper " . ($errors->has("formData" . $field->name) ? "has-error" : ""),
    ];
    $mergedAttributes = $field->getMergedAttributes($baseAttributes);

    // Convert array to HTML attributes string
    $attributesString = "";
    foreach ($mergedAttributes as $key => $value) {
        $attributesString .= $key . '="' . e($value) . '" ';
    }
@endphp

@unless ($field->isHidden())
    <div {!! $attributesString !!}>
        @if ($field->type !== "hidden")
            <label for="{{ $field->name }}" class="form-label">
                {{ $field->getLabel() }}
                @if ($field->required)
                    <span class="text-danger">*</span>
                @endif
            </label>
        @endif

        @if ($field->prefixIcon || $field->suffixIcon)
            <div class="relative">
                @if ($field->prefixIcon)
                    <span class="absolute start-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-laravel-simple-datatables-and-forms::icon :icon="$field->prefixIcon" class="h-5 w-5" />
                    </span>
                @endif

                <input
                    type="{{ $field->type }}"
                    id="{{ $field->name }}"
                    name="{{ $field->name }}"
                    @if ($field->reactive)
                        wire:model.live="formData.{{ $field->name }}"
                    @else
                        wire:model.defer="formData.{{ $field->name }}"
                    @endif
                    class="form-input {{ $field->disabled ? "cursor-not-allowed opacity-50" : "" }} {{ $field->prefixIcon ? "ps-10" : "" }} {{ $field->suffixIcon ? "pe-10" : "" }}"
                    @if ($field->placeholder) placeholder="{{ $field->placeholder }}" @endif
                    @if ($field->required) required @endif
                    @if ($field->disabled) disabled @endif
                    @if ($field->minLength) minlength="{{ $field->minLength }}" @endif
                    @if ($field->maxLength) maxlength="{{ $field->maxLength }}" @endif
                    @if ($field->pattern) pattern="{{ $field->pattern }}" @endif
                    @if ($field->min !== null) min="{{ $field->min }}" @endif
                    @if ($field->max !== null) max="{{ $field->max }}" @endif
                    @if ($field->step) step="{{ $field->step }}" @endif
                    @foreach ($field->attributes as $key => $value)
                        {{ $key }}="{!! $value !!}"
                    @endforeach
                />

                @if ($field->suffixIcon)
                    <span class="absolute end-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-laravel-simple-datatables-and-forms::icon :icon="$field->suffixIcon" class="h-5 w-5" />
                    </span>
                @endif
            </div>
        @else
            <input
                type="{{ $field->type }}"
                id="{{ $field->name }}"
                name="{{ $field->name }}"
                @if ($field->reactive)
                    wire:model.live="formData.{{ $field->name }}"
                @else
                    wire:model.defer="formData.{{ $field->name }}"
                @endif
                class="form-input {{ $field->disabled ? "cursor-not-allowed opacity-50" : "" }}"
                @if ($field->placeholder) placeholder="{{ $field->placeholder }}" @endif
                @if ($field->required) required @endif
                @if ($field->disabled) disabled @endif
                @if ($field->minLength) minlength="{{ $field->minLength }}" @endif
                @if ($field->maxLength) maxlength="{{ $field->maxLength }}" @endif
                @if ($field->pattern) pattern="{{ $field->pattern }}" @endif
                @if ($field->min !== null) min="{{ $field->min }}" @endif
                @if ($field->max !== null) max="{{ $field->max }}" @endif
                @if ($field->step)
                    step="{{ $field->step }}"
                @endif
                @foreach ($field->attributes as $key => $value)
                    {{ $key }}="{!! $value !!}"
                @endforeach
            />
        @endif

        @if ($field->helperText)
            <p class="form-help dark:text-white-dark mt-1 text-sm text-gray-600">{{ $field->helperText }}</p>
        @endif

        @error("formData." . $field->name)
            <p class="form-help text-danger mt-1 text-sm">{{ $message }}</p>
        @enderror
    </div>
@endunless
