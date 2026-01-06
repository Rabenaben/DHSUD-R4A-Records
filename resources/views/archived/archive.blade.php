<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Archived Files') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4">
        <!-- Section Header Card -->
        <x-section-header :title="__('Archived Documents Summary')" />

        <div class="rounded-xl border border-gray-300 bg-white p-4 shadow">
            <h3 class="mb-4 text-lg font-semibold text-black-600">Archived Records</h3>
            <!-- Search Filter Bar -->
            <div class="mb-4 flex items-center rounded-xl border border-gray-300 bg-gray-100 px-4 py-2">
                <input
                    class="w-full border-none bg-transparent text-gray-700 placeholder-gray-400 outline-none focus:ring-0"
                    id="archiveSearchInput" type="text"
                    placeholder="Search by Type, Docket No., or Name...">
            </div>
            <div class="max-h-[350px] overflow-x-auto overflow-y-auto">
                <table class="min-w-full table-fixed divide-y divide-gray-200 bg-white" id="archiveTable">
                    <thead class="sticky top-0 bg-red-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">Type</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">Docket No</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-red-50">
                        @foreach($remArchived ?? [] as $record)
                            <tr class="archive-row" data-type="REM" data-docket="{{ $record->docket_no }}" data-name="{{ $record->project_name }}" data-id="{{ $record->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">REM - {{ $record->province }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $record->docket_no }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->project_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <button class="unarchive-btn bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs" data-type="rem" data-id="{{ $record->id }}">
                                        Unarchive
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        @foreach($hoaArchived ?? [] as $record)
                            <tr class="archive-row" data-type="HOA" data-docket="{{ $record->docket_no }}" data-name="{{ $record->hoa_name }}" data-id="{{ $record->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">HOA</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $record->docket_no }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->hoa_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <button class="unarchive-btn bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs" data-type="hoa" data-id="{{ $record->id }}">
                                        Unarchive
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        @if(empty($remArchived) && empty($hoaArchived))
                            <tr id="no-archived-records-row">
                                <td class="px-6 py-4 text-center text-sm text-gray-500 italic" colspan="4">
                                    No archived records found
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-confirm-archive-file-modal />
</x-app-layout>
