<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('HOA Records') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4">
        <!-- Section Header Card -->
        <div class="relative bg-transparent">
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
                            class="wrap-break-word flex-1 whitespace-normal rounded-md bg-white px-3 py-1 text-center shadow-sm transition hover:bg-gray-200">
                            {{ $region }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <!-- Search bar -->
                <div class="flex flex-1 items-center rounded-xl border border-gray-300 bg-gray-100 px-4 py-2">
                    <input
                        class="w-full border-none bg-transparent text-gray-700 placeholder-gray-400 outline-none focus:ring-0"
                        id="searchInput" type="text"
                        placeholder="Search by Docket No, HOA Name, Location, Province, or Municipality...">
                </div>

                <!-- Status Filter -->
                <select class="rounded-xl border border-gray-300 bg-gray-100 px-4 py-2 text-gray-700" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="ON-SHELF">ON-SHELF</option>
                    <option value="BORROWED">BORROWED</option>
                    <option value="UNAVAILABLE">UNAVAILABLE</option>
                </select>

                <!-- Province Filter -->
                <select class="rounded-xl border border-gray-300 bg-gray-100 px-4 py-2 text-gray-700" id="provinceFilter">
                    <option value="">All Province</option>
                </select>

                <!-- Municipality Filter -->
                <select class="rounded-xl border border-gray-300 bg-gray-100 px-4 py-2 text-gray-700" id="municipalityFilter">
                    <option value="">All Municipality</option>
                </select>
            </div>

            <!-- Table Component -->
            <x-hoa.records-table :records="$hoaRecords ?? []" />
        </div>

        @include('hoa_records.partials.file-list-modal')
        @include('hoa_records.partials.hoa-modal')
</x-app-layout>
