@if ($errors->any())
    <div class="form-validation-summary" x-data="{ show: true }" x-show="show" x-transition>
        <div class="flex items-center justify-between">
            <div>
                <h3 class="form-validation-summary-title">
                    <svg class="mr-2 inline-block h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    {{ __('Please correct the following errors:') }}
                </h3>
            </div>
            <button type="button" @click="show = false" class="text-danger hover:text-danger/80">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                    />
                </svg>
            </button>
        </div>

        <ul class="form-validation-summary-list">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
