<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('HOA Records') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Section Header Card -->
        <div class="relative bg-transparent py-4">
            <h2 class="text-2xl font-bold tracking-wide text-black">{{ __('HOA Documents Summary') }}</h2>
            <div class="mt-2 border-b-2 border-gray-600"></div>
        </div>

        <div class="space-y-8">
            <!-- Status Cards -->
            @php $totalDockets = $totalHoaDockets; @endphp
            <x-status-cards :totalDockets="$totalDockets" :onShelf="$onShelf" :unavailable="$unavailable" :borrowed="$borrowed" theme="hoa" />

            <!-- Region Tabs -->
            <div class="flex items-center justify-between rounded-lg bg-gray-300 p-2 shadow-sm">
                <div class="flex w-full flex-wrap justify-between gap-3">
                    @foreach (['RIV', 'STR', 'RIZAL', 'CALABARZON', 'NCR HOA', 'NCR HOA N', 'R4A'] as $region)
                        <button
                            class="flex-1 whitespace-normal wrap-break-word rounded-md bg-white px-3 py-1 text-center shadow-sm transition hover:bg-gray-200">
                            {{ $region }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Folder Section -->
            <x-folder-section :provinces="$provinces" theme="hoa" />

            <!-- Dynamic Folder Table -->
            <div id="folderContent"></div>
        </div>

    </div>
</x-app-layout>
