<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4">

        <!-- Section Header Card -->
        <x-section-header :title="__('Documents Summary')" />

        <div class="space-y-4">

            <!-- Stats Grid -->
            <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3 xl:grid-cols-6">
                @foreach ($cards as $card)
                    @php
                        $bgClass = match($card['from']) {
                            'gray-600' => 'from-gray-600 to-gray-900',
                            'blue-500' => 'from-blue-500 to-blue-800',
                            'orange-400' => 'from-orange-400 to-orange-500',
                            'green-400' => 'from-green-400 to-green-700',
                            'red-500' => 'from-red-500 to-red-800',
                            'yellow-300' => 'from-yellow-300 to-yellow-600',
                            default => '',
                        };
                        $textClass = $card['text'] === 'text-white' ? 'text-white' : 'text-black';
                    @endphp
                    <div class="relative flex h-20 items-center justify-between rounded-lg bg-white p-3 shadow hover:transform hover:-translate-y-2 transition-transform duration-200">

                        <!-- LEFT COLORED BAR -->
                        <div class="bg-linear-to-r {{ $bgClass }} absolute bottom-0 left-0 top-0 w-2 rounded-l-lg">
                        </div>

                        <!-- Text Content -->
                        <div class="{{ $textClass }} flex flex-col pl-2">
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
            <div class="rounded-xl border border-gray-300 bg-white p-4 shadow">
                <h3 class="mb-4 text-lg font-semibold text-black-600">Recent Activity Logs</h3>
                <div class="max-h-[350px] overflow-x-auto overflow-y-auto">
                    <table class="min-w-full table-auto divide-y divide-gray-200 bg-white">
                        <thead class="sticky top-0 bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">Docket No.</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">File Name</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">File Location</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">Date &amp; Time</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">Action</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-white">User</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-gray-50 text-gray-700">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
