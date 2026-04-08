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
                <th class="w-1/6 px-6 py-3 text-center text-sm font-semibold text-white">Docket No</th>
                <th class="w-1/3 px-6 py-3 text-center text-sm font-semibold text-white">HOA Name</th>
                <th class="w-1/6 px-6 py-3 text-center text-sm font-semibold text-white">Location</th>
                <th class="w-1/6 px-6 py-3 text-center text-sm font-semibold text-white">Province</th>
                <th class="w-1/6 px-6 py-3 text-center text-sm font-semibold text-white">Municipality</th>
                <th class="w-1/6 px-6 py-3 text-center text-sm font-semibold text-white">Status</th>
                <th class="w-1/6 px-6 py-3 text-center text-sm font-semibold text-white">Action</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200" id="hoaRecordsTable">
            @forelse($records as $record)
                @php
                    $statusClass = $statusClasses[$record->status] ?? $statusClasses['DEFAULT'];
                @endphp
                <tr class="hoa-row @unless(auth()->user()->role === 'Staff') cursor-pointer @endunless transition hover:bg-blue-100" data-record='@json($record)'
                    @foreach (['docket_no', 'hoa_name', 'location', 'province', 'municipality', 'remarks', 'status'] as $col)
                            data-{{ $col }}="{{ strtolower(
                                $col === 'province'
                                    ? $record->province->province_name ?? ''
                                    : ($col === 'municipality'
                                        ? $record->municipality->municipality_name ?? ''
                                        : $record->{$col} ?? ''),
                            ) }}" @endforeach>
                    <td class="w-1/6 px-6 py-4 text-center text-sm text-gray-900">{{ $record->docket_no }}</td>
                    <td class="w-1/3 px-6 py-4 text-center text-sm text-gray-900">{{ $record->hoa_name }}</td>
                    <td class="w-1/6 px-6 py-4 text-center text-sm text-gray-900">{{ $record->location }}</td>
                    <td class="w-1/6 px-6 py-4 text-center text-sm text-gray-900">
                        {{ $record->province->province_name ?? 'N/A' }}</td>
                    <td class="w-1/6 px-6 py-4 text-center text-sm text-gray-900">
                        {{ $record->municipality->municipality_name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-center text-sm">
                        <span class="{{ $statusClass }} inline-flex rounded-full px-2 py-1 text-xs font-semibold">
                            {{ $record->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-gray-900">
                        <button type="button"
                            class="qr-btn rounded bg-blue-600 px-3 py-1 text-xs font-semibold text-white hover:bg-blue-700"
                            data-type="hoa"
                            data-record-id="{{ $record->id }}"
                            data-province-id="{{ $record->province_id }}"
                            data-municipality-id="{{ $record->municipality_id }}"
                            data-docket="{{ $record->docket_no }}">
                            QR Code
                        </button>
                    </td>
                </tr>
            @empty
            @endforelse

            <tr class="hidden" id="noRecordsRow">
                <td class="px-6 py-4 text-center text-sm text-gray-500" colspan="7">
                    No HOA records found
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Pagination Container for AJAX -->
<div id="hoa-pagination-container" class="mt-4 flex justify-end">
    @if($records && $records->hasPages())
        <div class="flex items-center space-x-2">
            <!-- Previous Button -->
            <button id="hoa-prev-page" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed" {{ $records->currentPage() <= 1 ? 'disabled' : '' }}>
                &larr;
            </button>

            <!-- Page Input -->
            <input type="number" id="hoa-page-input" value="{{ $records->currentPage() }}" min="1" max="{{ $records->lastPage() }}" data-current-page="{{ $records->currentPage() }}" class="w-16 px-2 py-1 border border-gray-300 rounded text-center">
            <span class="text-sm text-gray-600">of {{ $records->lastPage() }}</span>

            <!-- Next Button -->
            <button id="hoa-next-page" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed" {{ $records->currentPage() >= $records->lastPage() ? 'disabled' : '' }}>
                &rarr;
            </button>
        </div>
    @endif
</div>
