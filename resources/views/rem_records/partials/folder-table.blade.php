@props(['records' => []])

<div class="rounded-xl border border-gray-300 bg-white p-4 shadow">
    <div class="mb-4">
        <button class="rounded bg-gray-200 px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-300"
            id="backToFolders">
            &larr; Back to Folders
        </button>
    </div>
    <!-- Filters -->
    <div class="mb-4 flex flex-col space-y-2 md:flex-row md:items-center md:space-x-4 md:space-y-0">
        <!-- Search bar -->
        <div class="flex flex-1 items-center rounded-xl border border-gray-300 bg-gray-100 px-4 py-2">
            <input class="w-full border-none bg-transparent text-gray-700 placeholder-gray-400 outline-none focus:ring-0"
                id="remSearchInput" type="text" placeholder="Search by Docket or Project Name">
        </div>

        <!-- Status Filter -->
        <select class="rounded-xl border border-gray-300 bg-gray-100 px-4 py-2 text-gray-700" id="remStatusFilter">
            <option value="">All Status</option>
            <option value="ON-SHELF">ON-SHELF</option>
            <option value="BORROWED">BORROWED</option>
            <option value="UNAVAILABLE">UNAVAILABLE</option>
        </select>
    </div>

    <!-- Table -->
    <div class="max-h-[350px] overflow-x-auto overflow-y-auto">
        <table class="min-w-full table-fixed divide-y divide-gray-200 bg-white">
            <thead class="sticky top-0 bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Docket No</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Project Name</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Quantity</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Remarks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="remTableBody">
                @forelse($records as $record)
                    <tr class="data-row cursor-pointer transition hover:bg-gray-50" data-record='@json($record)'>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $record->docket_no }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $record->project_name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span @class([
                                'inline-flex rounded-full px-2 py-1 text-xs font-semibold',
                                'bg-green-100 text-green-800' => $record->status === 'ON-SHELF',
                                'bg-yellow-100 text-yellow-800' => $record->status === 'BORROWED',
                                'bg-red-100 text-red-800' => !in_array($record->status, [
                                    'ON-SHELF',
                                    'BORROWED',
                                ]),
                            ])>
                                {{ $record->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $record->quantity ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $record->remarks ?? '-' }}</td>
                    </tr>
                @empty
                @endforelse

                <!-- This row is always present -->
                <tr id="noRemRecordsRow">
                    <td class="px-6 py-4 text-center text-sm text-gray-500" colspan="5">
                        No REM records found
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- REM Modal -->
<x-modal name="rem" maxWidth="6xl">
    <button
        class="ml-2 mt-2 flex items-center gap-2 rounded-lg bg-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
        onclick="closeRemModal()">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        Close
    </button>
    <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-2">

        <!-- Form Section -->
        <div class="flex-1">
            <!-- Basic Information -->
            <h3 class="mb-2 mt-4 text-[15px] font-semibold">Basic Information</h3>
            <div class="mb-2.5 flex gap-2.5">
                <div class="flex-1 rounded-lg border border-gray-300 p-2">
                    <label class="block text-xs font-medium text-gray-700">Docket No.</label>
                    <input type="text" id="rem-docket-no" class="w-full border-none bg-transparent text-sm outline-none" readonly>
                </div>
                <div class="flex-1 rounded-lg border border-gray-300 p-2">
                    <label class="block text-xs font-medium text-gray-700">Project Name</label>
                    <input type="text" id="rem-project-name" class="w-full border-none bg-transparent text-sm outline-none" readonly>
                </div>
            </div>

            <!-- Additional Information -->
            <h3 class="mb-2 mt-4 text-[15px] font-semibold">Additional Information</h3>
            <div class="mb-2.5 flex gap-2.5">
                <div class="flex-1 rounded-lg border border-gray-300 p-2">
                    <label class="block text-xs font-medium text-gray-700">Status</label>
                    <input type="text" id="rem-status" class="w-full border-none bg-transparent text-sm outline-none" readonly>
                </div>
                <div class="flex-1 rounded-lg border border-gray-300 p-2">
                    <label class="block text-xs font-medium text-gray-700">Quantity</label>
                    <input type="text" id="rem-quantity" class="w-full border-none bg-transparent text-sm outline-none" readonly>
                </div>
            </div>

            <div class="rounded-lg border border-gray-300 p-2">
                <label class="block text-xs font-medium text-gray-700">Remarks</label>
                <textarea class="min-h-[50px] w-full resize-none border-none bg-transparent text-sm outline-none" id="rem-remarks" readonly></textarea>
            </div>

            <button class="mx-auto mt-4 block rounded-lg bg-green-600 px-6 py-2 text-sm font-semibold text-white transition hover:bg-green-700">EDIT</button>
        </div>

        <!-- File Section -->
        <div class="flex flex-1 flex-col items-center">
            <div class="h-[340px] w-[90%] rounded-lg border border-gray-300 bg-gray-100 bg-contain bg-center bg-no-repeat"
                id="rem-file-preview"
                style="background-image: url('https://via.placeholder.com/300x400?text=File+Preview')"></div>
            <div class="mb-4 mt-2 text-sm font-medium text-gray-800" id="rem-file-label"></div>
            <div class="flex gap-3">
                <button class="rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white hover:bg-blue-700">IMPORT FILE</button>
                <button class="rounded-lg bg-blue-800 px-6 py-2 font-semibold text-white hover:bg-blue-900">EXPORT FILE</button>
                <button class="rounded-lg bg-red-600 px-6 py-2 font-semibold text-white hover:bg-red-700">ARCHIVE FILE</button>
            </div>
        </div>
    </div>
</x-modal>
