<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="shadow-xs overflow-hidden bg-white sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>

                <div class="space-y-8">

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 gap-4 p-2 md:grid-cols-3 xl:grid-cols-6">

                        <!-- Total Dockets -->
                        <div
                            class="bg-linear-to-r flex flex-col items-center rounded-lg from-gray-600 to-gray-900 p-2 text-white shadow">
                            <div class="flex flex-row items-center">
                                <i class="bi bi-folder2-open text-2x1 mr-2"></i>
                                <p class="text-sm font-semibold">Total Dockets</p>
                            </div>
                            <h2 class="text-1x1 font-bold tracking-wider">{{ $totalDockets }}</h2>
                        </div>

                        <!-- Total REM Dockets -->
                        <div
                            class="bg-linear-to-r flex flex-col items-center rounded-lg from-blue-500 to-blue-800 p-2 text-white shadow">
                            <div class="flex flex-row items-center">
                                <i class="bi bi-gear-wide-connected text-2x1 mr-2"></i>
                                <p class="text-sm font-semibold">Total REM Dockets</p>
                            </div>
                            <h2 class="text-1x1 font-bold tracking-wider">{{ $totalRemDockets }}</h2>
                        </div>

                        <!-- Total HOA Dockets -->
                        <div
                            class="bg-linear-to-r flex flex-col items-center rounded-lg from-orange-400 to-orange-700 p-2 text-white shadow">
                            <div class="flex flex-row items-center">
                                <i class="bi bi-house-door-fill text-2x1 mr-2"></i>
                                <p class="text-sm font-semibold">Total HOA Dockets</p>
                            </div>
                            <h2 class="text-1x1 font-bold tracking-wider">{{ $totalHoaDockets }}</h2>
                        </div>

                        <!-- On-Shelf -->
                        <div
                            class="bg-linear-to-r flex flex-col items-center rounded-lg from-green-400 to-green-700 p-2 text-white shadow">
                            <div class="flex flex-row items-center">
                                <i class="bi bi-archive-fill text-2x1 mr-2"></i>
                                <p class="text-sm font-semibold">On-Shelf</p>
                            </div>
                            <h2 class="text-1x1 font-bold tracking-wider">{{ $onShelf }}</h2>
                        </div>

                        <!-- Unavailable -->
                        <div
                            class="bg-linear-to-r flex flex-col items-center rounded-lg from-yellow-300 to-yellow-600 p-2 text-black shadow">
                            <div class="flex flex-row items-center">
                                <i class="bi bi-file-earmark-x-fill text-2x1 mr-2"></i>
                                <p class="text-sm font-semibold">Unavailable</p>
                            </div>
                            <h2 class="text-1x1 font-bold tracking-wider">{{ $unavailable }}</h2>
                        </div>

                        <!-- Borrowed -->
                        <div
                            class="bg-linear-to-r flex flex-col items-center rounded-lg from-red-500 to-red-800 p-2 text-white shadow">
                            <div class="flex flex-row items-center">
                                <i class="bi bi-arrow-left-right text-2x1 mr-2"></i>
                                <p class="text-sm font-semibold">Borrowed</p>
                            </div>
                            <h2 class="text-1x1 font-bold tracking-wider">{{ $borrowed }}</h2>
                        </div>
                    </div>

                    <!-- Recently Opened Documents -->
                    <div class="rounded-lg bg-white p-6 shadow-md">
                        <h2 class="mb-4 text-xl font-bold">Recently Opened Documents</h2>

                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-300 text-center text-sm">
                                <thead class="bg-gray-800 text-white">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-2">File Name</th>
                                        <th class="border border-gray-300 px-4 py-2">File Type</th>
                                        <th class="border border-gray-300 px-4 py-2">File Location</th>
                                        <th class="border border-gray-300 px-4 py-2">Date &amp; Time</th>
                                        <th class="border border-gray-300 px-4 py-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white text-gray-700">
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2">-</td>
                                        <td class="border border-gray-300 px-4 py-2">-</td>
                                        <td class="border border-gray-300 px-4 py-2">-</td>
                                        <td class="border border-gray-300 px-4 py-2">-</td>
                                        <td class="border border-gray-300 px-4 py-2">-</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2">-</td>
                                        <td class="border border-gray-300 px-4 py-2">-</td>
                                        <td class="border border-gray-300 px-4 py-2">-</td>
                                        <td class="border border-gray-300 px-4 py-2">-</td>
                                        <td class="border border-gray-300 px-4 py-2">-</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2">-</td>
                                        <td class="border border-gray-300 px-4 py-2">-</td>
                                        <td class="border border-gray-300 px-4 py-2">-</td>
                                        <td class="border border-gray-300 px-4 py-2">-</td>
                                        <td class="border border-gray-300 px-4 py-2">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
