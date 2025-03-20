<div>
    <label for="{{ $filter->name }}" class="block text-sm font-medium text-gray-700">
        {{ $filter->label }}
    </label>
    <select
        id="{{ $filter->name }}" wire:model.live="filters.{{ $filter->name }}" class="form-select">
        <option value="">{{ __('Select an option') }}</option>
        @foreach ($filter->getOptions() as $key => $value)
            <option
                value="{{ $key }}"
                {{ in_array($key, $filter->component->filters[$filter->name] ?? []) ? 'selected' : '' }}
            >
                {{ $value }}
            </option>
        @endforeach
    </select>
</div>
