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
            <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-3 xl:grid-cols-6">
                @foreach ($cards as $card)
                    <div
                        class="bg-linear-to-r {{ $card['text'] }} from-{{ $card['from'] }} to-{{ $card['to'] }} flex h-20 items-center justify-between rounded-lg p-2 shadow">
                        <div class="flex flex-col text-left">
                            <h2 class="text-lg font-bold leading-tight md:text-xl">{{ $card['count'] }}</h2>
                            <p class="mt-1 text-xs font-semibold opacity-90 md:text-sm">{{ $card['title'] }}</p>
                        </div>
                        <i class="bi {{ $card['icon'] }} text-2xl"></i>
                    </div>
                @endforeach
            </div>

            <!-- Recently Opened Documents -->
            <div class="rounded-lg shadow-md">
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
</x-app-layout>
