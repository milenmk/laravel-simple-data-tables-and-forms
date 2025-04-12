@props([
    'formats' => ['csv'],
    'defaultFormat' => 'csv',
])

<button
    type="button"
    class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    wire:click="export('csv')"
>
    {{ __('Export to CSV') }}
</button>