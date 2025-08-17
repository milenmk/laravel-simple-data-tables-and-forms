@php
    $baseAttributes = [
        'class' => 'form-field-wrapper ' . ($errors->has('formData' . $field->name) ? 'has-error' : ''),
    ];
    $mergedAttributes = $field->getMergedAttributes($baseAttributes);

    // Convert array to HTML attributes string
    $attributesString = '';
    foreach ($mergedAttributes as $key => $value) {
        $attributesString .= $key . '="' . e($value) . '" ';
    }
@endphp

<div
    class="{!! $attributesString !!}"
    x-data="fileUpload(@entangle('formData.' . $field->name).defer, {
                multiple: {{ $field->multiple ? 'true' : 'false' }},
                maxFiles: {{ $field->maxFiles ?? 1 }},
                maxSize: {{ $field->maxSize ?? 'null' }},
                accept: '{{ $field->accept ?? '' }}',
            })"
>
    <label for="{{ $field->name }}" class="form-label">
        {{ $field->getLabel() }}
        @if ($field->required)
            <span class="text-danger">*</span>
        @endif
    </label>

    {{-- Drag and Drop Zone --}}
    <div
        class="file-upload-dropzone"
        :class="{ 'dragover': dragover }"
        @dragover.prevent="dragover = true"
        @dragleave.prevent="dragover = false"
        @drop.prevent="handleDrop($event)"
    >
        <input
            type="file"
            id="{{ $field->name }}"
            name="{{ $field->name }}"
            @change="handleFileSelect($event)"
            @if ($field->accept) accept="{{ $field->accept }}" @endif
            @if ($field->multiple) multiple @endif
            @if ($field->required) required @endif
            @if ($field->disabled) disabled @endif
        />

        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path
                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
            <div class="mt-4">
                <p class="dark:text-white-dark text-sm text-gray-600">
                    <span class="text-primary hover:text-primary/80 cursor-pointer font-medium">Click to upload</span>
                    or drag and drop
                </p>

                @if (! empty($field->acceptedFileTypes))
                    <p class="dark:text-white-dark mt-1 text-xs text-gray-500">
                        {{ implode(', ', $field->acceptedFileTypes) }}
                    </p>
                @endif

                @if ($field->maxSize)
                    <p class="dark:text-white-dark text-xs text-gray-500">up to {{ $field->maxSize }}KB</p>
                @endif
            </div>
        </div>
    </div>

    {{-- File Preview --}}
    <div x-show="files.length > 0" class="file-upload-preview" x-cloak>
        <template x-for="(file, index) in files" :key="index">
            <div class="file-upload-preview-item">
                <div class="file-upload-preview-remove" @click="removeFile(index)">×</div>
                <div class="text-center">
                    <div class="truncate text-xs font-medium" x-text="file.name"></div>
                    <div class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></div>
                </div>
            </div>
        </template>
    </div>

    @if ($field->helperText)
        <p class="form-help">{{ $field->helperText }}</p>
    @endif

    @error('formData.' . $field->name)
        <p class="form-help text-danger">{{ $message }}</p>
    @enderror
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('fileUpload', (model, options = {}) => ({
            files: [],
            dragover: false,

            init() {
                this.files = Array.isArray(model) ? model : model ? [model] : [];
            },

            handleFileSelect(event) {
                this.processFiles(event.target.files);
            },

            handleDrop(event) {
                this.dragover = false;
                this.processFiles(event.dataTransfer.files);
            },

            processFiles(fileList) {
                const newFiles = Array.from(fileList);

                // Check file count
                if (!options.multiple && newFiles.length > 1) {
                    alert('Only one file is allowed');
                    return;
                }

                if (options.multiple && this.files.length + newFiles.length > options.maxFiles) {
                    alert(`Maximum ${options.maxFiles} files allowed`);
                    return;
                }

                // Validate files
                for (const file of newFiles) {
                    // Check file size
                    if (options.maxSize && file.size > options.maxSize * 1024) {
                        alert(`File "${file.name}" is too large. Maximum size is ${options.maxSize}KB`);
                        continue;
                    }

                    // Check file type
                    if (options.accept && !this.isAcceptedType(file, options.accept)) {
                        alert(`File type "${file.type}" is not allowed`);
                        continue;
                    }

                    if (options.multiple) {
                        this.files.push(file);
                    } else {
                        this.files = [file];
                    }
                }

                this.updateModel();
            },

            removeFile(index) {
                this.files.splice(index, 1);
                this.updateModel();
            },

            updateModel() {
                model = options.multiple ? this.files : this.files[0] || null;
            },

            isAcceptedType(file, accept) {
                const acceptedTypes = accept.split(',').map((type) => type.trim());
                return acceptedTypes.some((type) => {
                    if (type.startsWith('.')) {
                        return file.name.toLowerCase().endsWith(type.toLowerCase());
                    }
                    return file.type.match(type.replace('*', '.*'));
                });
            },

            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            },
        }));
    });
</script>
