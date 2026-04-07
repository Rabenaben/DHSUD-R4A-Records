<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Archived Files') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4">
        <!-- Section Header Card -->
        <x-section-header :title="__('Archived Documents')" />
        <div class="space-y-4">
            <!-- Search Filter Bar -->
            <div class="mb-4 mt-4 flex items-center rounded-xl border border-gray-300 bg-gray-100 px-4 py-2">
                <input
                    class="w-full border-none bg-transparent text-gray-700 placeholder-gray-400 outline-none focus:ring-0"
                    id="archiveSearchInput" type="text" placeholder="Search by Type, Docket No., or Name...">
            </div>
            <div class="max-h-[350px] overflow-x-auto overflow-y-auto rounded-xl border border-gray-300 bg-white shadow">
                <table class="min-w-full table-fixed divide-y divide-gray-200 bg-white" id="archiveTable">
                    <thead class="sticky top-0 bg-red-600 z-10">
                        <tr>
                            <th class="w-20 px-6 py-3 text-center text-sm font-semibold text-white">Type</th>
                            <th class="w-32 px-6 py-3 text-center text-sm font-semibold text-white">Docket No</th>
                            <th class="w-40 px-6 py-3 text-center text-sm font-semibold text-white">Record Name</th>
                            <th class="w-auto px-6 py-3 text-center text-sm font-semibold text-white">Archived Files
                            </th>
                            <th class="w-36 px-6 py-3 text-center text-sm font-semibold text-white">Last Archive Date</th>
                            <th class="w-36 px-6 py-3 text-center text-sm font-semibold text-white">Last Updated By</th>
                            <th class="w-24 px-6 py-3 text-center text-sm font-semibold text-white">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-red-50">
                        @foreach ($archivedDockets ?? [] as $docket)
                            <tr class="archive-row @unless (auth()->user()->role === 'Staff') cursor-pointer @endunless transition hover:bg-blue-100"
                                data-type="{{ strtoupper($docket['type']) }}" data-docket="{{ $docket['docket_no'] }}"
                                data-name="{{ $docket['record_name'] }}"
                                data-file="{{ $docket['archived_count'] }} file{{ $docket['archived_count'] != 1 ? 's' : '' }}">
                                <td class="px-6 py-4 text-center text-sm text-gray-500">
                                    {{ strtoupper($docket['type']) }}</td>
                                <td class="px-6 py-4 text-center text-sm font-medium text-gray-900">
                                    {{ $docket['docket_no'] }}</td>
                                <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $docket['record_name'] }}
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $docket['archived_count'] }}
                                    file{{ $docket['archived_count'] != 1 ? 's' : '' }}
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-500">
                                    {{ $docket['date_added'] ? \Carbon\Carbon::parse($docket['date_added'])->format('M d, Y H:i') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-500">
                                    {{ $docket['last_updated_by'] ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 text-center text-sm text-gray-500">
                                    <button
                                        class="unarchive-file-btn rounded bg-green-500 px-3 py-1 text-xs text-white hover:bg-green-600"
                                        data-type="{{ $docket['type'] }}" data-docket="{{ $docket['docket_no'] }}">
                                        Unarchive Docket
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        @if (empty($archivedDockets ?? []))
                            <tr id="no-archived-records-row">
                                <td class="px-6 py-4 text-center text-sm italic text-gray-500" colspan="7">
                                    No archived files found
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('archived.partials.archive-modal')
    <x-confirm-archive-file-modal />
</x-app-layout>
