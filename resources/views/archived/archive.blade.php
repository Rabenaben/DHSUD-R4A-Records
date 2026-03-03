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
                    id="archiveSearchInput" type="text"
                    placeholder="Search by Type, Docket No., or Name...">
            </div>
            <div class="max-h-[350px] overflow-x-auto overflow-y-auto rounded-xl border-gray-300 bg-white p-4 shadow">
                <table class="min-w-full table-fixed divide-y divide-gray-200 bg-white" id="archiveTable">
                    <thead class="sticky top-0 bg-red-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">Type</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">Docket No</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">Record Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">File Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">Date Added</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">Last Updated By</th>
                            @unless(auth()->user()->role === 'Staff')
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">Action</th>
                            @endunless
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-red-50">
                        @foreach($archivedFiles ?? [] as $file)
                            <tr class="archive-row @unless(auth()->user()->role === 'Staff') cursor-pointer @endunless transition hover:bg-blue-100" data-type="{{ strtoupper($file['type']) }}" data-docket="{{ $file['docket_no'] }}" data-name="{{ $file['record_name'] }}" data-file="{{ $file['file_name'] }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ strtoupper($file['type']) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $file['docket_no'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $file['record_name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $file['file_name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $file['date_added'] ? \Carbon\Carbon::parse($file['date_added'])->format('M d, Y H:i') : 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $file['last_updated_by'] ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @unless(auth()->user()->role === 'Staff')
                                    <button class="unarchive-file-btn bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs" data-type="{{ $file['type'] }}" data-docket="{{ $file['docket_no'] }}" data-file-index="{{ $file['file_index'] }}">
                                        Unarchive
                                    </button>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                        @if(empty($archivedFiles))
                            <tr id="no-archived-records-row">
                                <td class="px-6 py-4 text-center text-sm text-gray-500 italic" colspan="7">
                                    No archived files found
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-confirm-archive-file-modal />
    @include('archived.partials.archive-modal')
</x-app-layout>
