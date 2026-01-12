<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('HOA Records') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4">
        <!-- Section Header Card -->
        <x-section-header :title="__('HOA Documents Summary')" />

        <div class="space-y-4">
            <!-- Status Cards -->
            @php $totalDockets = $totalHoaDockets; @endphp
            <x-status-cards :totalDockets="$totalDockets" :onShelf="$onShelf" :unavailable="$unavailable" :borrowed="$borrowed" theme="hoa" />

            <!-- Region Tabs -->
            <div class="flex items-center justify-between rounded-lg bg-gray-300 p-2 shadow-sm">
                <div class="flex w-full flex-wrap justify-between gap-3">
                    @foreach (['RIV', 'STR', 'RIZAL', 'CALABARZON', 'NCR HOA', 'NCR HOA N', 'R4A'] as $region)
                        <button
                            class="region-btn wrap-break-word flex-1 whitespace-normal rounded-md bg-white px-3 py-1 text-center shadow-sm transition hover:bg-gray-200"
                            data-region="{{ $region }}">
                            {{ $region }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Region Filter Status -->
            <div class="mt-2 text-sm text-gray-600" id="region-filter-status" style="display: none;">
                Showing <span id="filtered-count">0</span> of <span id="total-count">0</span> records for region: <span id="active-region" class="font-semibold"></span>
            </div>

            @include('hoa_records.partials.search-filter-bar')

            <!-- Table Component -->
            <x-hoa.records-table :records="$hoaRecords ?? []" />
        </div>

        @include('hoa_records.partials.hoa-modal')
        <x-add-record-modal type="hoa" :provinces="$provinces" />
        <x-add-file-modal />
        <x-save-file-modal />
        <x-confirm-archive-file-modal />
        <x-confirm-save-record-modal type="HOA" />

</x-app-layout>
