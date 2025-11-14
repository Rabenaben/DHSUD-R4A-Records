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
            </div>

            <!-- Table Component -->
            <x-hoa-records-table :records="$hoaRecords ?? []" />
        </div>

        <!-- File List Modal -->
        <x-modal name="file-list" maxWidth="4xl">
            <div class="mb-4 flex items-center justify-between p-6">
                <h3 class="text-lg font-semibold text-gray-900" id="file-list-title">Files</h3>
                <button class="text-gray-400 hover:text-gray-600"
                    @click="$dispatch('close-modal', { name: 'file-list' })">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                File Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                Date Modified</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white" id="file-list-body">
                        {{-- Files will be rendered here via JS --}}
                    </tbody>
                </table>
            </div>
        </x-modal>

        <!-- HOA Modal -->
        <x-modal name="hoa" maxWidth="4xl">
            <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-2">

                <!-- Form Section -->
                <div class="flex-1">
                    <!-- Basic Information -->
                    <h3 class="mb-2 mt-4 text-[15px] font-semibold">Basic Information</h3>
                    <div class="mb-2.5 flex gap-2.5">
                        <input
                            class="flex-1 rounded-lg border border-gray-300 px-2 py-2 outline-none focus:border-blue-600"
                            id="docket-no" type="text" placeholder="Docket No." readonly>
                        <input
                            class="flex-1 rounded-lg border border-gray-300 px-2 py-2 outline-none focus:border-blue-600"
                            id="hoa-name" type="text" placeholder="HOA Name" readonly>
                    </div>

                    <!-- Location -->
                    <h3 class="mb-2 mt-4 text-[15px] font-semibold">Location</h3>
                    <div class="mb-2.5 flex gap-2.5">
                        <input
                            class="flex-1 rounded-lg border border-gray-300 px-2 py-2 outline-none focus:border-blue-600"
                            id="province" type="text" placeholder="Province" readonly>
                        <input
                            class="flex-1 rounded-lg border border-gray-300 px-2 py-2 outline-none focus:border-blue-600"
                            id="municipality" type="text" placeholder="Municipality" readonly>
                    </div>

                    <!-- Additional Information -->
                    <h3 class="mb-2 mt-4 text-[15px] font-semibold">Additional Information</h3>
                    <div class="mb-2.5 flex gap-2.5">
                        <input
                            class="flex-1 rounded-lg border border-gray-300 px-2 py-2 outline-none focus:border-blue-600"
                            id="status" type="text" placeholder="Status" readonly>
                        <input
                            class="flex-1 rounded-lg border border-gray-300 px-2 py-2 outline-none focus:border-blue-600"
                            id="quantity" type="text" placeholder="Quantity" readonly>
                    </div>

                    <textarea
                        class="min-h-[50px] w-full resize-none rounded-lg border border-gray-300 p-2 outline-none focus:border-blue-600"
                        id="remarks" placeholder="Remarks" readonly></textarea>

                    <button
                        class="mx-auto mt-4 block rounded-lg bg-green-600 px-6 py-2 text-sm font-semibold text-white transition hover:bg-green-700">EDIT</button>
                </div>

                <!-- File Section -->
                <div class="flex flex-1 flex-col items-center">
                    <div class="h-[340px] w-[90%] rounded-lg border border-gray-300 bg-gray-100 bg-contain bg-center bg-no-repeat"
                        id="file-preview"
                        style="background-image: url('https://via.placeholder.com/300x400?text=File+Preview')"></div>
                    <div class="mb-4 mt-2 text-sm font-medium text-gray-800" id="file-label"></div>
                    <div class="flex gap-3">
                        <button
                            class="rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white hover:bg-blue-700">IMPORT
                            FILE</button>
                        <button
                            class="rounded-lg bg-blue-800 px-6 py-2 font-semibold text-white hover:bg-blue-900">EXPORT
                            FILE</button>
                        <button
                            class="rounded-lg bg-red-600 px-6 py-2 font-semibold text-white hover:bg-red-700">ARCHIVE
                            FILE</button>
                    </div>
                </div>
            </div>
        </x-modal>
</x-app-layout>
