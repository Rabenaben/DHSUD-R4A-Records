<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('REM Records') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-6 text-gray-900">
                {{ __("You're logged in!") }}
            </div>
            
            <div class="space-y-8">
                <!-- Status Cards -->
                @php $totalDockets = $totalRemDockets; @endphp
                <x-status-cards :totalDockets="$totalDockets" :onShelf="$onShelf" :unavailable="$unavailable" :borrowed="$borrowed" theme="rem" />

                <!-- Folder Section -->
                <x-folder-section :provinces="$provinces" theme="rem" />

                <!-- Dynamic Folder Table -->
                <div id="folderContent"></div>

                <!-- Borrowed History Table (static) -->
                <div class="rounded-xl border border-gray-300 bg-white p-4 shadow">
                    <h2 class="mb-2 text-lg font-bold text-gray-800">Borrowed History</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300">
                            <thead>
                                <tr class="bg-red-500 text-sm text-white">
                                    <th class="border border-gray-300 px-4 py-2 text-left">Borrower</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Date Borrowed</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Date Returned</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border px-4 py-2">&nbsp;</td>
                                    <td class="border px-4 py-2">&nbsp;</td>
                                    <td class="border px-4 py-2">&nbsp;</td>
                                    <td class="border px-4 py-2">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="border px-4 py-2">&nbsp;</td>
                                    <td class="border px-4 py-2">&nbsp;</td>
                                    <td class="border px-4 py-2">&nbsp;</td>
                                    <td class="border px-4 py-2">&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
