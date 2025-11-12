<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('HOA Records') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-6 text-gray-900">
                {{ __("You're logged in!") }}
            </div>

            <div class="space-y-8">
                <!-- Status Cards -->
                @php $totalDockets = $totalHoaDockets; @endphp
                <x-status-cards :totalDockets="$totalDockets" :onShelf="$onShelf" :unavailable="$unavailable" :borrowed="$borrowed" theme="hoa" />

                <!-- Region Tabs and Search Bar -->
                <div class="flex items-center rounded-lg bg-gray-300 p-2 shadow-sm">
                    <div class="flex shrink-0 flex-wrap gap-2">
                        @foreach (['RIV', 'STR', 'RIZAL', 'CALABARZON', 'NCR HOA', 'NCR HOA N', 'R4A'] as $region)
                            <button
                                class="rounded-md bg-white px-3 py-1 shadow-sm transition hover:bg-gray-200">{{ $region }}</button>
                        @endforeach
                    </div>
                    <div class="relative ml-4 flex-1">
                        <input
                            class="w-full rounded-lg border py-2 pl-10 pr-4 shadow-sm focus:outline-none focus:ring focus:ring-blue-200"
                            type="text" placeholder="Search">
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35M9.5 17A7.5 7.5 0 109.5 2a7.5 7.5 0 000 15z" />
                        </svg>
                    </div>
                </div>

                <!-- Folder Section -->
                <x-folder-section :provinces="$provinces" theme="hoa" />

                <!-- Dynamic Folder Table -->
                <div id="folderContent"></div>
            </div>

        </div>
    </div>
</x-app-layout>
