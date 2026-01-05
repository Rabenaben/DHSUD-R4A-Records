<!-- Add File Modal -->
<x-modal name="add-file" maxWidth="md">
    <div class="p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Add New File</h2>

        <form id="add-file-form" enctype="multipart/form-data">
            <input type="hidden" id="docket-no-hidden" name="docket_no" />

            <div class="mb-4">
                <x-input-label value="File Name" />
                <x-modal-input id="file-name" name="file_name" placeholder="Enter file name" required />
            </div>

            <div class="mb-4">
                <x-input-label value="Upload PDF File" />
                <input type="file" id="file-upload" name="file" accept=".pdf"
                    class="w-full rounded-lg border border-gray-300 p-2 outline-none focus:border-blue-600"
                    required />
            </div>

            <div class="mb-6">
                <x-input-label value="Date Added" />
                <input type="date" id="date-added" name="date_added"
                    class="w-full rounded-lg border border-gray-300 p-2 outline-none focus:border-blue-600"
                    required />
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
