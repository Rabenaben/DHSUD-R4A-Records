<div class="rounded-xl border border-gray-300 bg-white p-4 shadow">
    <h2 class="mb-2 text-lg font-bold text-gray-800">
        {{ strtoupper($province) }} {{ strtoupper($type ?? '') }} Dockets
    </h2>

    <!-- Filters -->
    <div class="mb-4 flex items-center space-x-4">
        <!-- Search bar -->
        <div class="flex flex-1 items-center rounded-xl border border-gray-300 bg-gray-100 px-4 py-2">
            <input class="w-full border-none bg-transparent text-gray-700 placeholder-gray-400 outline-none focus:ring-0"
                id="searchInput" type="text" placeholder="Search by Docket or HOA Name">
        </div>

        <!-- Status Filter -->
        <select class="rounded-xl border border-gray-300 bg-gray-100 px-4 py-2 text-gray-700" id="statusFilter">
            <option value="">All Status</option>
            <option value="ON-SHELF">ON-SHELF</option>
            <option value="BORROWED">BORROWED</option>
            <option value="UNAVAILABLE">UNAVAILABLE</option>
        </select>
    </div>

    <div class="max-h-[350px] min-h-[350px] overflow-x-auto overflow-y-auto">
        <table class="min-w-full table-fixed border border-gray-300">
            <thead class="sticky top-0">
                <tr class="bg-blue-700 text-sm text-white">
                    <th class="w-1/5 border border-gray-300 px-4 py-2 text-center">Docket No</th>
                    <th class="w-3/5 border border-gray-300 px-4 py-2 text-center">Project Name</th>
                    <th class="w-1/5 border border-gray-300 px-4 py-2 text-center">Status</th>
                </tr>
            </thead>
            <tbody id="folderTableBody">
                @foreach ($records as $record)
                    <tr class="data-row transition hover:bg-blue-100">
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $record->docket_no }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            {{ $record->project_name ?? ($record->hoa_name ?? 'N/A') }}
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $record->status }}</td>
                    </tr>
                @endforeach
                <tr class="hidden" id="noRecordsRow">
                    <td class="px-4 py-6 text-center text-gray-500" colspan="3">
                        No Records Found
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
