<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4">

        <!-- Section Header Card -->
        <div class="relative bg-transparent">
            <h2 class="text-2xl font-bold tracking-wide text-black">{{ __('Documents Summary') }}</h2>
            <div class="mt-2 border-b-2 border-gray-600"></div>
        </div>

        <div class="space-y-8">

            <!-- Stats Grid -->
            <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3 xl:grid-cols-6">
                @foreach ($cards as $card)
                    <div class="relative flex h-20 items-center justify-between rounded-lg bg-white p-3 shadow">

                        <!-- LEFT COLORED BAR -->
                        <div
                            class="bg-linear-to-r {{ $card['from'] === 'gray-600' ? 'from-gray-600 to-gray-900' : '' }} {{ $card['from'] === 'blue-500' ? 'from-blue-500 to-blue-800' : '' }} {{ $card['from'] === 'orange-400' ? 'from-orange-400 to-orange-500' : '' }} {{ $card['from'] === 'green-400' ? 'from-green-400 to-green-700' : '' }} {{ $card['from'] === 'red-500' ? 'from-red-500 to-red-800' : '' }} {{ $card['from'] === 'yellow-300' ? 'from-yellow-300 to-yellow-600' : '' }} absolute bottom-0 left-0 top-0 w-2 rounded-l-lg">
                        </div>

                        <!-- Text Content -->
                        <div
                            class="{{ $card['text'] === 'text-white' ? 'text-white' : 'text-black' }} flex flex-col pl-2">
                            <h2 class="text-lg font-bold leading-tight md:text-xl">
                                {{ $card['count'] }}
                            </h2>
                            <p class="mt-1 text-xs font-semibold md:text-sm">
                                {{ $card['title'] }}
                            </p>
                        </div>

                        <!-- Icon -->
                        <i class="bi {{ $card['icon'] }} pr-1 text-2xl text-gray-700"></i>
                    </div>
                @endforeach
            </div>

            <!-- Recent Activity Logs -->
            <div class="rounded-lg shadow-md">
                <h2 class="mb-4 text-xl font-bold">Recent Activity Logs</h2>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300 text-center text-sm">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">Docket No.</th>
                                <th class="border border-gray-300 px-4 py-2">File Name</th>
                                <th class="border border-gray-300 px-4 py-2">File Location</th>
                                <th class="border border-gray-300 px-4 py-2">Date &amp; Time</th>
                                <th class="border border-gray-300 px-4 py-2">Action</th>
                                <th class="border border-gray-300 px-4 py-2">User</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white text-gray-700">
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">-</td>
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
                                <td class="border border-gray-300 px-4 py-2">-</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">-</td>
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
</x-app-layout>
