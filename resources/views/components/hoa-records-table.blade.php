@props(['records' => []])

@php
    $statusClasses = [
        'ON-SHELF' => 'bg-green-100 text-green-800',
        'BORROWED' => 'bg-yellow-100 text-yellow-800',
        'DEFAULT' => 'bg-red-100 text-red-800',
    ];
@endphp

<div class="max-h-70 overflow-x-auto rounded-lg shadow">
    <table class="min-w-full table-fixed divide-y divide-gray-200 bg-white">
        <thead class="sticky top-0 z-10 bg-blue-700">
            <tr>
                @foreach (['Docket No', 'HOA Name', 'Location', 'Province', 'Municipality', 'Status', 'Quantity', 'Remarks'] as $header)
                    <th class="px-6 py-3 text-center text-sm font-semibold text-white">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200" id="hoaRecordsTable">
            @forelse($records as $record)
                @php
                    $statusClass = $statusClasses[$record->status] ?? $statusClasses['DEFAULT'];
                @endphp
                <tr class="transition hover:bg-blue-100"
                    @foreach (['docket_no', 'hoa_name', 'location', 'province', 'municipality', 'remarks', 'status'] as $col)
                        data-{{ $col }}="{{ strtolower(
                            $col === 'province'
                                ? $record->province->province_name ?? ''
                                : ($col === 'municipality'
                                    ? $record->municipality->municipality_name ?? ''
                                    : $record->{$col} ?? ''),
                        ) }}" @endforeach>
                    <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $record->docket_no }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $record->hoa_name }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $record->location }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-900">
                        {{ $record->province->province_name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-900">
                        {{ $record->municipality->municipality_name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-center text-sm">
                        <span class="{{ $statusClass }} inline-flex rounded-full px-2 py-1 text-xs font-semibold">
                            {{ $record->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $record->quantity }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $record->remarks ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td class="px-6 py-4 text-center text-sm text-gray-500" colspan="8">No HOA records found</td>
                </tr>
            @endforelse

            <tr class="hidden" id="noBorrowedRow">
                <td class="px-6 py-4 text-center text-sm font-semibold text-gray-500" colspan="8">
                    No Borrowed Records
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const tableRows = document.querySelectorAll('#hoaRecordsTable tr:not(#noBorrowedRow)');
    const noBorrowedRow = document.getElementById('noBorrowedRow');

    function filterTable() {
        const query = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value.toLowerCase();
        let anyVisible = false;

        tableRows.forEach(row => {
            const data = row.dataset;
            const matchesSearch = Object.values(data).some(val => val.includes(query));
            const matchesStatus = !selectedStatus || data.status === selectedStatus;

            row.style.display = matchesSearch && matchesStatus ? '' : 'none';
            if (matchesSearch && matchesStatus) anyVisible = true;
        });

        noBorrowedRow.classList.toggle('hidden', anyVisible || selectedStatus !== 'borrowed');
    }

    searchInput.addEventListener('keyup', filterTable);
    statusFilter.addEventListener('change', filterTable);
</script>
