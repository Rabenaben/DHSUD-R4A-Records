<!-- Add File Modal -->
<x-modal name="add-file" maxWidth="md">
    <div class="p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Add New File</h2>

        <form id="add-file-form" enctype="multipart/form-data">
            <input type="hidden" id="docket-no-hidden" name="docket_no" />

            <div class="mb-4">
                <x-input-label value="Upload PDF Files (Bulk)" />
                <div class="relative">
                    <input type="file" id="file-upload" name="files[]" accept=".pdf" multiple
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        required />
                    <div id="file-display" class="w-full rounded-lg border border-gray-300 p-2 bg-white cursor-pointer">
                        No files chosen
                    </div>
                </div>
                <div id="selected-files" class="mt-2 text-sm text-gray-600"></div>
            </div>



            <div class="flex justify-end gap-3">
                <button type="button" id="cancel-add-file-btn"
                    class="rounded-lg bg-gray-500 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Add File
                </button>
            </div>
        </form>
    </div>
</x-modal>
