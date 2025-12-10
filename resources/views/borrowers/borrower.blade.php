<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Borrowers') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4">
        <!-- Section Header Card -->
        <div class="relative bg-transparent">
            <h2 class="text-2xl font-bold tracking-wide text-black">{{ __('Borrower Records') }}</h2>
            <div class="mt-2 border-b-2 border-gray-600"></div>
        </div>

        <div class="space-y-8">
            <!-- Search Bar -->
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center rounded-xl border border-gray-300 bg-gray-100 px-4 py-2 min-w-0 flex-1">
                    <input
                        class="w-full border-none bg-transparent text-gray-700 placeholder-gray-400 outline-none focus:ring-0"
                        id="searchInput" type="text"
                        placeholder="Search by ID, Borrower Name...">
                </div>
                <button
                    id="add-record-btn"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Add Record
                </button>
            </div>

            <!-- Borrower Table -->
            <div class="rounded-lg bg-white p-6 shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                           <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrower Name</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($borrowers as $borrower)
                        <tr data-id="{{ $borrower->id }}" data-borrower-name="{{ $borrower->borrower_name }}" onclick="showBorrowerDetails({{ $borrower->id }})" style="cursor: pointer;">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $borrower->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $borrower->borrower_name }}</td>
                        </tr>
                        @endforeach
                        <tr id="noRecordsRow" class="hidden">
                            <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">No records found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('borrowers.partials.borrower-modal', ['recordStatuses' => $recordStatuses, 'nextId' => $nextId])

<script>
window.nextId = {{ $nextId }};
</script>
</x-app-layout>
