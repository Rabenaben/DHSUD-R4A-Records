@props(['records' => []])

<!-- HOA Records Table -->
<div class="max-h-70 overflow-x-auto rounded-lg shadow">
    <table class="min-w-full table-fixed divide-y divide-gray-200 bg-white">
        <thead class="sticky top-0 z-10 bg-blue-700">
            <tr>
                <th class="w-2/12 px-6 py-3 text-center text-sm font-semibold text-white">Docket No</th>
                <th class="w-2/12 px-6 py-3 text-center text-sm font-semibold text-white">HOA Name</th>
                <th class="w-1/12 px-6 py-3 text-center text-sm font-semibold text-white">Location</th>
                <th class="w-1/12 px-6 py-3 text-center text-sm font-semibold text-white">Province</th>
                <th class="w-1/12 px-6 py-3 text-center text-sm font-semibold text-white">Municipality</th>
                <th class="w-1/12 px-6 py-3 text-center text-sm font-semibold text-white">Status</th>
                <th class="w-1/12 px-6 py-3 text-center text-sm font-semibold text-white">Quantity</th>
                <th class="w-2/12 px-6 py-3 text-center text-sm font-semibold text-white">Remarks</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200" id="hoaRecordsTable">
            @forelse($records as $record)
                <tr class="transition hover:bg-blue-100" data-docket="{{ strtolower($record->docket_no) }}"
                    data-hoaname="{{ strtolower($record->hoa_name) }}"
                    data-location="{{ strtolower($record->location) }}"
                    data-province="{{ strtolower($record->province->province_name ?? '') }}"
                    data-municipality="{{ strtolower($record->municipality->municipality_name ?? '') }}"
                    data-remarks="{{ strtolower($record->remarks ?? '') }}"
                    data-status="{{ strtolower($record->status) }}">

                    <!-- Fixed width columns -->
                    <td class="w-2/12 px-6 py-4 text-center text-sm text-gray-900">{{ $record->docket_no }}</td>
                    <td class="w-2/12 px-6 py-4 text-center text-sm text-gray-900">{{ $record->hoa_name }}</td>

                    <!-- Auto width columns -->
                    <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $record->location }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-900">
                        {{ $record->province->province_name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-900">
                        {{ $record->municipality->municipality_name ?? 'N/A' }}</td>
                    <td class="w-2/12 px-6 py-4 text-center text-sm">
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
                    <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $record->quantity }}</td>

                    <!-- Fixed width column -->
                    <td class="w-2/12 px-6 py-4 text-center text-sm text-gray-900">{{ $record->remarks ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td class="px-6 py-4 text-center text-sm text-gray-500" colspan="8">
                        No HOA records found
                    </td>
                </tr>
            @endforelse

            <!-- Placeholder for No Borrowed Records -->
            <tr class="hidden" id="noBorrowedRow">
                <td class="px-6 py-4 text-center text-sm font-semibold text-gray-500" colspan="8">
                    No Borrowed Records
                </td>
            </tr>
        </tbody>


    </table>

    <script>
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const tableRows = document.querySelectorAll('#hoaRecordsTable tr');
        const noBorrowedRow = document.getElementById('noBorrowedRow');

        function filterTable() {
            const query = searchInput.value.toLowerCase();
            const selectedStatus = statusFilter.value.toLowerCase();

            let anyRowVisible = false;

            tableRows.forEach(row => {
                if (row.id === 'noBorrowedRow') return; // skip placeholder

                const docket = row.dataset.docket || '';
                const hoaname = row.dataset.hoaname || '';
                const location = row.dataset.location || '';
                const province = row.dataset.province || '';
                const municipality = row.dataset.municipality || '';
                const remarks = row.dataset.remarks || '';
                const status = row.dataset.status || '';

                // Match search query in multiple columns
                const matchesSearch =
                    docket.includes(query) ||
                    hoaname.includes(query) ||
                    location.includes(query) ||
                    province.includes(query) ||
                    municipality.includes(query) ||
                    remarks.includes(query);

                // Match status dropdown
                const matchesStatus = selectedStatus === '' || status === selectedStatus;

                row.style.display = matchesSearch && matchesStatus ? '' : 'none';

                if (matchesSearch && matchesStatus) {
                    anyRowVisible = true;
                }
            });

            // Show "No Borrowed Records" if no visible rows AND BORROWED is selected
            if (!anyRowVisible && selectedStatus === 'borrowed') {
                noBorrowedRow.classList.remove('hidden');
            } else {
                noBorrowedRow.classList.add('hidden');
            }
        }

        searchInput.addEventListener('keyup', filterTable);
        statusFilter.addEventListener('change', filterTable);
    </script>

</div>
