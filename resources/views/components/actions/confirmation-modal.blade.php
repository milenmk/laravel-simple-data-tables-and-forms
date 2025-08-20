<div
    class="fixed inset-0 z-[999] overflow-y-auto bg-[black]/60"
    x-show="actionConfirmModal"
    x-cloak
    style="display: none"
>
    <div class="flex min-h-screen items-center justify-center px-4" @click.self="actionConfirmModal = false">
        <div
            x-show="actionConfirmModal"
            x-transition
            x-transition.duration.300
            class="my-8 w-full max-w-lg overflow-hidden rounded-lg bg-white shadow-lg dark:bg-gray-900"
        >
            <div class="flex items-center justify-between bg-[#fbfbfb] px-5 py-3 text-wrap dark:bg-[#121c2c]">
                <h5 class="text-lg font-bold uppercase" x-html="modalData.heading"></h5>
                <div class="text-danger h-6 w-6">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                        />
                    </svg>
                </div>
            </div>
            <div class="p-5">
                <template x-if="modalData.description">
                    <div class="text-md mb-4 font-bold text-wrap uppercase" x-html="modalData.description"></div>
                </template>
                <template x-if="modalData.content">
                    <div
                        class="dark:text-white-dark/70 text-base font-medium text-wrap text-[#1f2937]"
                        x-html="modalData.content"
                    ></div>
                </template>
                <div class="mt-8 flex items-center justify-end space-x-2">
                    <button type="button" @click="confirmModalAction()" class="btn btn-danger">
                        <span
                            x-show="modalData.confirmationButtonLabel"
                            x-html="modalData.confirmationButtonLabel"
                        ></span>
                        <span x-show="!modalData.confirmationButtonLabel">{{ __('Confirm') }}</span>
                    </button>
                    <button
                        type="button"
                        @click="actionConfirmModal = false; modalData = {}"
                        class="bg-black-700 relative flex items-center justify-center rounded-md border border-black px-5 py-2 text-sm font-semibold uppercase shadow-[0_10px_20px_-10px] shadow-black/60 outline-hidden transition duration-300 hover:shadow-none"
                    >
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
