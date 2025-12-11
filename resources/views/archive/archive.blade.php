<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Archived Files') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4">
        <!-- Section Header Card -->
        <x-section-header :title="__('Archived Documents Summary')" />

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <!-- Left Panel -->
            <div class="rounded-xl border border-gray-300 bg-white p-4 shadow">
                <h3 class="mb-4 text-lg font-semibold text-red-600">REM Records</h3>
                <div class="max-h-[350px] overflow-x-auto overflow-y-auto">
                    <table class="min-w-full table-fixed divide-y divide-gray-200 bg-white">
                        <thead class="sticky top-0 bg-red-600">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">Docket No</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">Project Name</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">Quantity</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-red-50">
                            <!-- Archived records would go here -->
                            <tr>
                                <td class="px-6 py-4 text-center text-sm text-gray-500 italic" colspan="5">
                                    No archived records found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Panel -->
            <div class="rounded-xl border border-gray-300 bg-white p-4 shadow">
                <h3 class="mb-4 text-lg font-semibold text-black-600">HOA Records</h3>
                <div class="max-h-[350px] overflow-x-auto overflow-y-auto">
                    <table class="min-w-full table-fixed divide-y divide-gray-200 bg-white">
                        <thead class="sticky top-0 bg-red-600">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">Docket No</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">Project Name</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">Quantity</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-red-50">
                            <!-- Archived records would go here -->
                            <tr>
                                <td class="px-6 py-4 text-center text-sm text-gray-500 italic" colspan="5">
                                    No archived records found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
