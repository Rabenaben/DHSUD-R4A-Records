<x-modal name="borrowed-records" maxWidth="4xl">
    <div class="p-0">
        <!-- Modal Header -->
        <div class="bg-linear-to-r from-yellow-400 to-yellow-600 px-6 py-4 text-white">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold" id="modal-title">
                    Borrowed Dockets
                </h3>
                <button type="button" class="text-white hover:text-gray-200 text-2xl font-bold" id="closeBorrowedModal"
                    onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'borrowed-records' } }))">
                    ×
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <div class="max-h-[500px] overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 bg-white">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Docket No.</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-64">Docket Name</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Type</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="borrowedRecordsTableBody">
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500 text-lg">Loading borrowed records...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-modal>
