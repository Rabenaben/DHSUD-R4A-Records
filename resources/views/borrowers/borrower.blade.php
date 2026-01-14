<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Borrowers') }}
        </h2>
    </x-slot>

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
                @unless(auth()->user()->role === 'Staff')
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                    id="add-record-btn">
                    Add Record
                </button>
                @endunless
            </div>

            <!-- Borrower Table -->
            <div class="mt-4 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-white p-6">
                    <table class="w-full divide-y divide-blue-400" id="borrowers-table">
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach (['ID', 'Borrower Name', 'Status'] as $header)
                                    <th
                                        class="text-black-500 px-6 py-3 text-center text-xs font-bold uppercase tracking-wider">
                                        {{ $header }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($borrowers as $index => $borrower)
                                <tr data-id="{{ $borrower->id }}" data-borrower-name="{{ $borrower->borrower_name }}"
                                    data-docket-number="{{ $borrower->docket_number }}"
                                    data-file-location="{{ $borrower->file_location }}"
                                    data-date-borrowed="{{ $borrower->date_borrowed }}"
                                    data-date-returned="{{ $borrower->date_returned }}"
                                    data-status="{{ $borrower->status }}"
                                    class="cursor-pointer hover:bg-gray-50"
                                    onclick="editBorrower({{ $borrower->id }})">
                                    <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-gray-500">
                                        {{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</td>
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-center text-sm font-medium text-gray-900">
                                        {{ $borrower->borrower_name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-gray-500">
                                        {{ $borrower->status }}</td>
                                </tr>
                            @empty
                                <tr id="noRecordsRow">
                                    <td class="p-3 text-center text-sm text-gray-500" colspan="3">No records found.
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
        ])

        @include('borrowers.partials.borrower-records-modal', [
            'hoaDockets' => $hoaDockets,
            'remDockets' => $remDockets,
        ])

        <script>
            window.nextId = {{ $nextId }};
        </script>
</x-app-layout>
