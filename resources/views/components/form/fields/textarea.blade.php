<div class="form-field-wrapper {{ $errors->has('formData.' . $field->name) ? 'has-error' : '' }}">
    <label for="{{ $field->name }}" class="form-label">
        {{ $field->getLabel() }}
        @if ($field->required)
            <span class="text-danger">*</span>
        @endif
    </label>

    @php
        $attributes = '';

        foreach ($field->attributes as $key => $value) {
            $attributes .= $key . '="' . e($value) . '" ';
        }
    @endphp

    @if ($field->prefixIcon || $field->suffixIcon)
        <div class="relative">
            @if ($field->prefixIcon)
                <span class="absolute start-3 top-3 text-gray-400">
                    <x-laravel-simple-datatables-and-forms::icon :icon="$field->prefixIcon" class="h-5 w-5" />
                </span>
            @endif

            <textarea
                id="{{ $field->name }}"
                name="{{ $field->name }}"
                wire:model.defer="formData.{{ $field->name }}"
                class="form-textarea {{ $field->disabled ? 'cursor-not-allowed opacity-50' : '' }} {{ $field->autosize ? 'autosize' : '' }} {{ $field->prefixIcon ? 'ps-10' : '' }} {{ $field->suffixIcon ? 'pe-10' : '' }}"
                rows="{{ $field->rows }}"
                cols="{{ $field->cols }}"
                @if ($field->placeholder) placeholder="{{ $field->placeholder }}" @endif
                @if ($field->required) required @endif
                @if ($field->disabled) disabled @endif
                @if ($field->minLength) minlength="{{ $field->minLength }}" @endif
                @if ($field->maxLength) maxlength="{{ $field->maxLength }}" @endif
                {{ $attributes }}
            ></textarea>

            @if ($field->suffixIcon)
                <span class="absolute end-3 top-3 text-gray-400">
                    <x-laravel-simple-datatables-and-forms::icon :icon="$field->suffixIcon" class="h-5 w-5" />
                </span>
            @endif
        </div>
    @else
        <textarea
            id="{{ $field->name }}"
            name="{{ $field->name }}"
            wire:model.defer="formData.{{ $field->name }}"
            class="form-textarea {{ $field->disabled ? 'cursor-not-allowed opacity-50' : '' }} {{ $field->autosize ? 'autosize' : '' }}"
            rows="{{ $field->rows }}"
            cols="{{ $field->cols }}"
            @if ($field->placeholder) placeholder="{{ $field->placeholder }}" @endif
            @if ($field->required) required @endif
            @if ($field->disabled) disabled @endif
            @if ($field->minLength) minlength="{{ $field->minLength }}" @endif
            @if ($field->maxLength) maxlength="{{ $field->maxLength }}" @endif
            {{ $attributes }}
        ></textarea>
    @endif

    @if ($field->helperText)
        <p class="form-help dark:text-white-dark mt-1 text-sm text-gray-600">{{ $field->helperText }}</p>
    @endif

    @error('formData.' . $field->name)
        <p class="form-help text-danger mt-1 text-sm">{{ $message }}</p>
    @enderror
</div>

@if ($field->autosize)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const textarea = document.getElementById('{{ $field->name }}');
            if (textarea) {
                function autoResize() {
                    textarea.style.height = 'auto';
                    textarea.style.height = textarea.scrollHeight + 'px';
                }

                textarea.addEventListener('input', autoResize);
                autoResize(); // Initial resize
            }
        });
    </script>
@endif
