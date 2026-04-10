@props(['records' => []])

<div class="rounded-xl border border-gray-300 bg-white p-4 shadow">
    <div class="mb-4 flex items-center space-x-3">
        <button class="rounded bg-gray-200 px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-300"
            id="backToFolders">
            &larr; Back to Folders
        </button>
        <span id="currentProvinceDisplay" class="text-sm font-medium text-gray-700 hidden"></span>
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
            <option value="ARCHIVED">ARCHIVED</option>
        </select>

        <!-- Municipality Filter -->
        <select class="rounded-xl border border-gray-300 bg-gray-100 px-4 py-2 text-gray-700" id="remMunicipalityFilter">
            <option value="">All Municipalities</option>
            @php
                $municipalities = $records->pluck('municipality.municipality_name')->filter()->unique()->sort()->values();
            @endphp
            @foreach($municipalities as $municipality)
                <option value="{{ $municipality }}">{{ $municipality }}</option>
            @endforeach
        </select>

        <!-- Add Docket Button -->
        @unless(auth()->user()->role === 'Staff')
        <button class="rounded-xl border border-gray-300 bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" id="addRemDocketBtn">Add Docket</button>
        @endunless
    </div>

    <!-- Table -->
    <div class="max-h-[350px] overflow-x-auto overflow-y-auto">
        <table class="min-w-full table-fixed divide-y divide-gray-200 bg-white">
            <thead class="sticky top-0 bg-gray-100">
                <tr>
                    <th class="w-1/6 px-6 py-3 text-center text-sm font-semibold text-gray-700">Docket No</th>
                    <th class="w-1/6 px-6 py-3 text-center text-sm font-semibold text-gray-700">Project Name</th>
                    <th class="w-1/6 px-6 py-3 text-center text-sm font-semibold text-gray-700">Province</th>
                    <th class="w-1/6 px-6 py-3 text-center text-sm font-semibold text-gray-700">Municipality</th>
                    <th class="w-1/6 px-6 py-3 text-center text-sm font-semibold text-gray-700">Status</th>
                    <th class="w-1/6 px-6 py-3 text-center text-sm font-semibold text-gray-700">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="remTableBody">
                @forelse($records as $record)
                    <tr class="data-row @unless(auth()->user()->role === 'Staff') cursor-pointer @endunless transition hover:bg-gray-50" data-record='@json($record)'>
                        <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $record->docket_no }}</td>
                        <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $record->project_name ?? '-' }}</td>
                        <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $record->province->province_name ?? '-' }}</td>
                        <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $record->municipality->municipality_name ?? '-' }}</td>
                        <td class="px-6 py-4 text-center text-sm">
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

                        <td class="px-6 py-4 text-center text-sm text-gray-900">
                            <button type="button"
                                class="qr-btn rounded bg-blue-600 px-3 py-1 text-xs font-semibold text-white hover:bg-blue-700"
                                data-type="rem"
                                data-record-id="{{ $record->id }}"
                                data-province-id="{{ $record->province_id }}"
                                data-municipality-id="{{ $record->municipality_id }}"
                                data-docket="{{ $record->docket_no }}">
                                Genrate QR Code
                            </button>
                        </td>
                    </tr>
                @empty
                @endforelse

                <!-- This row is always present -->
                <tr id="noRemRecordsRow">
                    <td class="px-6 py-4 text-center text-sm text-gray-500" colspan="6">
                        No REM records found
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
