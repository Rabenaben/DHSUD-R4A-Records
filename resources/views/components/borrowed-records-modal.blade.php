<div id="borrowedRecordsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white text-left shadow-2xl transition-all sm:my-8">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 px-6 py-4 text-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold" id="modal-title">
                        Borrowed Dockets
                    </h3>
                    <button type="button" class="close-modal text-white hover:text-gray-200 text-2xl font-bold" id="closeBorrowedModal">
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
    </div>
</div>

<style>
#borrowedRecordsModal .close-modal {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 2rem;
    line-height: 1;
    position: absolute;
    right: 1rem;
    top: 1rem;
}
</style>
