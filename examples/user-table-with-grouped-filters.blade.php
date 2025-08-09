<div>
    <div class="mb-6">
        <h2 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">User Table with Grouped Filters Example</h2>
        <p class="mb-4 text-gray-600 dark:text-gray-400">
            This example demonstrates how to group filters together. The suspension status filters are grouped together
            in one column, user status filters in another column, and individual filters take their own columns.
        </p>

        <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
            <h3 class="mb-2 text-lg font-semibold text-blue-900 dark:text-blue-100">Filter Layout (4 columns):</h3>
            <ul class="space-y-1 text-blue-800 dark:text-blue-200">
                <li>
                    <strong>Column 1:</strong>
                    Suspension Status Group (3 filters stacked vertically)
                </li>
                <li>
                    <strong>Column 2:</strong>
                    User Status Group (2 filters stacked vertically)
                </li>
                <li>
                    <strong>Column 3:</strong>
                    Plan Filter (individual)
                </li>
                <li>
                    <strong>Column 4:</strong>
                    Created Month Filter (individual)
                </li>
            </ul>
        </div>
    </div>

    {{ $this->table }}
</div>
