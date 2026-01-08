<!-- Save File Confirmation Modal -->
<x-modal name="save-file" maxWidth="md">
    <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Confirm File Upload</h2>
        <p class="text-gray-600 mb-6">Are you sure you want to upload this file? This action will save the selected file to the system.</p>

        <div class="flex justify-end gap-3">
            <button
                id="cancel-save-btn"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                Cancel
            </button>
            <button
                id="confirm-save-btn"
                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                Save File
            </button>
        </div>
    </div>
</x-modal>
