@php
    $baseAttributes = [
        'class' => 'form-field-wrapper repeater-field ' . ($errors->has('formData' . $field->name) ? 'has-error' : ''),
    ];
    $mergedAttributes = $field->getMergedAttributes($baseAttributes);

    // Convert array to HTML attributes string
    $attributesString = '';
    foreach ($mergedAttributes as $key => $value) {
        $attributesString .= $key . '="' . e($value) . '" ';
    }
@endphp

<div
    {!! $attributesString !!}
    x-data="repeater(@entangle('formData.' . $field->name).defer)"
>
    @if ($field->getLabel())
        <label class="form-label">
            {{ $field->getLabel() }}
            @if ($field->required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="repeater-items space-y-4">
        <template x-for="(item, index) in items" :key="index">
            <div
                class="repeater-item"
                :class="{ 'collapsed': item.collapsed && {{ $field->collapsible ? 'true' : 'false' }} }"
            >
                @if ($field->collapsible)
                    <div
                        class="repeater-header mb-4 flex cursor-pointer items-center justify-between"
                        @click="item.collapsed = !item.collapsed"
                    >
                        <h4 class="font-medium text-gray-900">
                            Item
                            <span x-text="index + 1"></span>
                        </h4>
                        <button type="button" class="text-gray-500 hover:text-gray-700">
                            <svg x-show="!item.collapsed" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    fill-rule="evenodd"
                                    d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <svg x-show="item.collapsed" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </div>
                @endif

                <div
                    class="repeater-content"
                    x-show="! item.collapsed || ! {{ $field->collapsible ? 'true' : 'false' }}"
                >
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        @foreach ($field->schema as $schemaField)
                            <div class="repeater-field">
                                {{-- This would need to be dynamic based on field type --}}
                                <input
                                    type="text"
                                    :name="`formData.{{ $field->name }}[${index}].{{ $schemaField->name }}`"
                                    x-model="item.{{ $schemaField->name }}"
                                    class="field-input"
                                    placeholder="{{ $schemaField->placeholder ?? $schemaField->getLabel() }}"
                                />
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="repeater-actions mt-4 flex justify-end space-x-2">
                    @if ($field->cloneable)
                        <button type="button" @click="cloneItem(index)" class="btn btn-sm btn-outline">Clone</button>
                    @endif

                    @if ($field->deletable)
                        <button
                            type="button"
                            @click="removeItem(index)"
                            :disabled="items.length <= {{ $field->minItems }}"
                            class="btn btn-sm btn-outline-danger"
                            :class="{ 'opacity-50 cursor-not-allowed': items.length <= {{ $field->minItems }} }"
                        >
                            {{ $field->deleteLabel }}
                        </button>
                    @endif
                </div>
            </div>
        </template>
    </div>

    <div class="repeater-add-button mt-4">
        <button
            type="button"
            @click="addItem()"
            :disabled="items.length >= {{ $field->maxItems }}"
            class="btn btn-outline-primary"
            :class="{ 'opacity-50 cursor-not-allowed': items.length >= {{ $field->maxItems }} }"
        >
            + {{ $field->addLabel }}
        </button>
    </div>

    @if ($field->helperText)
        <p class="field-helper-text">{{ $field->helperText }}</p>
    @endif

    @error('formData.' . $field->name)
        <p class="field-error">{{ $message }}</p>
    @enderror
</div>

<script>
    function repeater(value) {
        return {
            items: value || [],

            addItem() {
                if (this.items.length < {{ $field->maxItems }}) {
                    this.items.push({
                        @foreach ($field->schema as $schemaField)
                                {{ $schemaField->name }}: '{{ $schemaField->default ?? "" }}',
                        @endforeach
                        collapsed: false
                    });
                }
            },

            removeItem(index) {
                if (this.items.length > {{ $field->minItems }}) {
                    this.items.splice(index, 1);
                }
            },

            cloneItem(index) {
                if (this.items.length < {{ $field->maxItems }}) {
                    this.items.push({...this.items[index], collapsed: false});
                }
            }
        }
    }
</script>
