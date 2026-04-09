<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Borrowers') }}
        </h2>
    </x-slot>

    <!-- Borrower Loading Overlay -->
    <div id="borrower-loading-overlay" class="fixed inset-0 z-50 hidden bg-black/30 flex-col items-center justify-center">
        <div class="bg-white p-8 rounded-xl shadow-2xl max-w-sm w-full mx-4 text-center border-4 border-blue-200">
            <div class="flex flex-col items-center space-y-4">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                <div class="space-y-2">
                    <h3 class="text-lg font-semibold text-gray-900">Loading Borrower Record</h3>
                    <p class="text-sm text-gray-600">Please wait while we fetch the history...</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl p-4">
        <!-- Section Header Card -->
        <x-section-header :title="__('Borrower Records')" />

        <div class="space-y-4">
            <!-- Search Bar -->
            <div class="mb-4 mt-2 flex flex-wrap items-center justify-between gap-3">
                <div class="flex min-w-0 flex-1 items-center rounded-xl border border-gray-300 bg-gray-100 px-4 py-2">
                    <input
                        class="w-full border-none bg-transparent text-gray-700 placeholder-gray-400 outline-none focus:ring-0"
                        id="searchInput" type="text" placeholder="Search by ID, Borrower Name, Docket No...">
                </div>
                <div class="flex items-center rounded-xl border border-gray-300 bg-gray-100 px-4 py-2">
                    <select id="divisionFilter" class="w-full border-none bg-transparent text-gray-700 outline-none focus:ring-0">
                        <option value="all">All Divisions</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division }}">{{ $division }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                    id="add-record-btn">
                    Add Record
                </button>
            </div>

            <!-- Borrower Table -->
            <div class="mt-4 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-white p-6">
                    <table class="w-full divide-y divide-blue-400" id="borrowers-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-1/6 px-6 py-3 text-center text-xs font-bold uppercase tracking-wider text-black-500">
                                    # of Borrowed Records
                                </th>
                                <th class="w-1/3 px-6 py-3 text-center text-xs font-bold uppercase tracking-wider text-black-500">
                                    Borrower Name
                                </th>
                                <th class="w-1/6 px-6 py-3 text-center text-xs font-bold uppercase tracking-wider text-black-500">
                                    Division
                                </th>
                                <th class="w-1/6 px-6 py-3 text-center text-xs font-bold uppercase tracking-wider text-black-500">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($borrowers as $borrower)
                                <tr data-id="{{ $borrower->id }}" data-borrower-name="{{ $borrower->borrower_name }}"
                                    data-docket-number="{{ $borrower->docket_number }}"
                                    data-file-location="{{ $borrower->file_location }}"
                                    data-division="{{ $borrower->division }}"
                                    data-date-borrowed="{{ $borrower->date_borrowed }}"
                                    data-date-returned="{{ $borrower->date_returned }}"
                                    data-status="{{ $borrower->status }}"
                                    class="cursor-pointer hover:bg-gray-50"
                                    onclick="editBorrower({{ $borrower->id }})">
                                    <td class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ $borrower->borrowed_count ?? 0 }}</td>
                                    <td class="px-6 py-4 text-center text-sm font-medium text-gray-900">
                                        {{ $borrower->borrower_name }}</td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ $borrower->division ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ $borrower->status }}</td>
                                </tr>
                            @empty
                                <tr id="noRecordsRow">
                                    <td class="p-3 text-center text-sm text-gray-500" colspan="4">No records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @include('borrowers.partials.borrower-modal', [
            'nextId' => $nextId,
            'hoaDockets' => $hoaDockets,
            'remDockets' => $remDockets,
            'divisions' => $divisions,
        ])

        @include('borrowers.partials.borrower-records-modal', [
            'hoaDockets' => $hoaDockets,
            'remDockets' => $remDockets,
        ])

        <script>
            window.nextId = {{ $nextId }};
            
            // Auto-open from notification (delegates to borrower.js)
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    if (window.initAutoOpenFromNotification) {
                        window.initAutoOpenFromNotification();
                    }
                }, 200);
            });
        </script>
</x-app-layout>
