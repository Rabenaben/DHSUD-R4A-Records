<!-- Borrower Record History Modal -->
<x-modal name="borrower-record-history" maxWidth="6xl">
    <div class="relative p-6">
        <!-- Close button -->
        <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-600" type="button"
            onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'borrower-record-history' } }))">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h2 class="mb-4 text-xl font-semibold" id="history-modal-title">Borrower Record History</h2>

        <!-- Borrower's Name -->
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900" id="borrower-name-display">Borrower's Name</h3>
            <input id="history-borrower-name" type="hidden" name="borrower_name">
            <input id="history-borrower-id" type="hidden" name="borrower_id">
        </div>

        <!-- Borrowing History Table -->
        <div class="mb-6">
            <h4 class="text-md mb-2 font-medium text-gray-700">Borrowing History</h4>
            <p class="text-xs text-gray-500 mb-2">You can click on the <span class="font-semibold">N/A</span> date in the Date Returned column to record a return date.</p>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                Docket No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                File Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                Date Borrowed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                Date Returned</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white" id="borrower-history-table">
                        <!-- History rows will be populated here -->
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="mt-4 flex justify-center" id="pagination-container" style="display: none;">
                <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <button
                        class="relative inline-flex items-center rounded-l-md border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50"
                        id="prev-page">
                        <span class="sr-only">Previous</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <span
                        class="relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700"
                        id="page-info"></span>
                    <button
                        class="relative inline-flex items-center rounded-r-md border border-gray-300 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50"
                        id="next-page">
                        <span class="sr-only">Next</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Add New Borrowing Record Button -->
        <div class="border-t pt-6">
            <div class="flex justify-end">
                @unless(auth()->user()->role === 'Staff')
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    id="add-new-record-btn" type="button">
                    Add New Borrowing Record
                </button>
                @endunless
            </div>
        </div>

    </div>
</x-modal>

<!-- Docket Status Verification Modal -->
<x-modal name="verify-returned-date-modal" maxWidth="sm">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">Confirm Return Record</h2>
        <div class="mt-4 space-y-4">
            <!-- Name (full width) -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm"
                    id="verify-borrower-name" type="text" readonly />
            </div>
            <!-- Row 2: Docket No | Date -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Docket No</label>
                    <input class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm"
                        id="verify-docket-no" type="text" readonly />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date</label>
                    <input class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm"
                        id="verify-returned-date" type="date" readonly />
                </div>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button class="mr-3 rounded-md bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400" type="button"
                onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'verify-returned-date-modal' } }))">
                Cancel
            </button>
            <button class="rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-900" id="verify-returned-date-btn"
                type="button">
                Returned
            </button>
        </div>
    </div>
</x-modal>

<!-- Confirm Returned Date Modal -->
<x-modal name="confirm-returned-date-modal" maxWidth="sm">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">Confirm Returned Date</h2>
        <p class="mt-4 text-sm text-gray-600">Are you sure you want to set this returned date? This action cannot be
            undone.</p>
        <div class="mt-6 flex justify-end">
            <button class="mr-3 rounded-md bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400" type="button"
                x-on:click="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-returned-date-modal' } }))">
                No
            </button>
            <button class="rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-900"
                id="confirm-returned-yes-btn" type="button">
                Yes
            </button>
        </div>
    </div>
</x-modal>
