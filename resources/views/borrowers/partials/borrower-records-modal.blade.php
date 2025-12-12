<!-- Borrower Record History Modal -->
<x-modal name="borrower-record-history" maxWidth="6xl">
    <div class="relative p-6">
        <!-- Close button -->
        <button type="button"
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
            onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'borrower-record-history' } }))">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h2 class="mb-4 text-xl font-semibold" id="history-modal-title">Borrower Record History</h2>

        <!-- Borrower's Name -->
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900" id="borrower-name-display">Borrower's Name</h3>
            <input type="hidden" id="history-borrower-name" name="borrower_name">
        </div>

        <!-- Borrowing History Table -->
        <div class="mb-6">
            <h4 class="text-md font-medium text-gray-700 mb-2">Borrowing History</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Docket No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Borrowed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Returned</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="borrower-history-table">
                        <!-- History rows will be populated here -->
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="mt-4 flex justify-center" id="pagination-container" style="display: none;">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <button id="prev-page" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Previous</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <span id="page-info" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"></span>
                    <button id="next-page" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Next</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Add New Borrowing Record Button -->
        <div class="border-t pt-6">
            <div class="flex justify-end">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    id="add-new-record-btn" type="button">
                    Add New Borrowing Record
                </button>
            </div>
        </div>

    </div>
</x-modal>

<!-- Confirm Returned Date Modal -->
<x-modal name="confirm-returned-date-modal" maxWidth="sm">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">Confirm Returned Date</h2>
        <p class="mt-4 text-sm text-gray-600">Are you sure you want to set this returned date? This action cannot be undone.</p>
        <div class="mt-6 flex justify-end">
            <button class="mr-3 rounded-md bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400" type="button"
                x-on:click="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-returned-date-modal' } }))">
                No
            </button>
            <button class="rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-900" id="confirm-returned-yes-btn"
                type="button">
                Yes
            </button>
        </div>
    </div>
</x-modal>
