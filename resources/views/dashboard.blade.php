<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Section Header Card -->
            <div class="relative bg-transparent py-4">
                <h2 class="text-2xl font-bold tracking-wide text-black">{{ __("Documents Summary") }}</h2>
                <div class="mt-2 border-b-2 border-gray-600"></div>
            </div>

            <div class="space-y-8">

                <!-- Stats Grid -->
                <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3 xl:grid-cols-6">

                    <!-- Total Dockets -->
                    <div
                        class="bg-linear-to-r flex h-20 items-center justify-between rounded-lg from-gray-600 to-gray-900 p-2 text-white shadow">
                        <div class="flex flex-col text-left">
                            <h2 class="text-lg font-bold leading-tight md:text-xl">{{ $totalDockets }}</h2>
                            <p class="mt-1 text-xs font-semibold opacity-90 md:text-sm">Total Dockets</p>
                        </div>
                        <i class="bi bi-folder2-open text-2xl"></i>
                    </div>

                    <!-- Total REM Dockets -->
                    <div
                        class="bg-linear-to-r flex h-20 items-center justify-between rounded-lg from-blue-500 to-blue-800 p-2 text-white shadow">
                        <div class="flex flex-col text-left">
                            <h2 class="text-lg font-bold leading-tight md:text-xl">{{ $totalRemDockets }}</h2>
                            <p class="mt-1 text-xs font-semibold opacity-90 md:text-sm">Total REM Dockets</p>
                        </div>
                        <i class="bi bi-gear-wide-connected text-2xl"></i>
                    </div>

                    <!-- Total HOA Dockets -->
                    <div
                        class="bg-linear-to-r flex h-20 items-center justify-between rounded-lg from-orange-400 to-orange-700 p-2 text-white shadow">
                        <div class="flex flex-col text-left">
                            <h2 class="text-lg font-bold leading-tight md:text-xl">{{ $totalHoaDockets }}</h2>
                            <p class="mt-1 text-xs font-semibold opacity-90 md:text-sm">Total HOA Dockets</p>
                        </div>
                        <i class="bi bi-house-door-fill text-2xl"></i>
                    </div>

                    <!-- On-Shelf -->
                    <div
                        class="bg-linear-to-r flex h-20 items-center justify-between rounded-lg from-green-400 to-green-700 p-2 text-white shadow">
                        <div class="flex flex-col text-left">
                            <h2 class="text-lg font-bold leading-tight md:text-xl">{{ $onShelf }}</h2>
                            <p class="mt-1 text-xs font-semibold opacity-90 md:text-sm">On-Shelf</p>
                        </div>
                        <i class="bi bi-archive-fill text-2xl"></i>
                    </div>

                    <!-- Unavailable -->
                    <div
                        class="bg-linear-to-r flex h-20 items-center justify-between rounded-lg from-yellow-300 to-yellow-600 p-2 text-black shadow">
                        <div class="flex flex-col text-left">
                            <h2 class="text-lg font-bold leading-tight md:text-xl">{{ $unavailable }}</h2>
                            <p class="mt-1 text-xs font-semibold opacity-90 md:text-sm">Unavailable</p>
                        </div>
                        <i class="bi bi-file-earmark-x-fill text-2xl"></i>
                    </div>

                    <!-- Borrowed -->
                    <div
                        class="bg-linear-to-r flex h-20 items-center justify-between rounded-lg from-red-500 to-red-800 p-2 text-white shadow">
                        <div class="flex flex-col text-left">
                            <h2 class="text-lg font-bold leading-tight md:text-xl">{{ $borrowed }}</h2>
                            <p class="mt-1 text-xs font-semibold opacity-90 md:text-sm">Borrowed</p>
                        </div>
                        <i class="bi bi-arrow-left-right text-2xl"></i>
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
</x-app-layout>
