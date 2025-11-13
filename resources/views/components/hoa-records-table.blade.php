@props(['records' => []])

<!-- HOA Records Table -->
<div class="overflow-x-auto rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200 bg-white">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Docket No</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">HOA Name</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Location</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Province</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Municipality</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Quantity</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Remarks</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($records as $record)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $record->docket_no }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $record->hoa_name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $record->location }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $record->province->province_name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $record->municipality->municipality_name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            @if($record->status === 'ON-SHELF')
                                bg-green-100 text-green-800
                            @elseif($record->status === 'BORROWED')
                                bg-yellow-100 text-yellow-800
                            @else
                                bg-red-100 text-red-800
                            @endif
                        ">
                            {{ $record->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $record->quantity }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $record->remarks ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                        No HOA records found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
