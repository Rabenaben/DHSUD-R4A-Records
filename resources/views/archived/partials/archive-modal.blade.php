<!-- Archive Modal -->
<x-modal name="archive" maxWidth="7xl">
    <button
        class="ml-2 mt-2 flex items-center gap-2 rounded-lg bg-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
        onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'archive' } }))">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        Close
    </button>
    <div class="flex flex-col gap-6 p-4 lg:flex-row min-h-[90vh] max-h-[90vh] overflow-hidden">

        <!-- Form Section -->
        <div class="flex-1/4">
            <!-- Basic Information -->
            <h3 class="mb-2 mt-4 text-[15px] font-semibold">Basic Information</h3>
            <div class="mb-2.5">
                <div class="mb-2.5">
                    <x-input-label value="Type" />
                    <x-modal-input id="archive-type" placeholder="Type" readonly />
                </div>
                <div class="mb-2.5">
                    <x-input-label value="Docket No." />
                    <x-modal-input id="archive-docket-no" placeholder="Docket No." readonly />
                </div>
                <div class="mb-2.5">
                    <x-input-label value="Record Name" />
                    <x-modal-input id="archive-record-name" placeholder="Record Name" readonly />
                </div>
                <div class="mb-2.5">
                    <x-input-label value="File Name" />
                    <x-modal-input id="archive-file-name" placeholder="File Name" readonly />
                </div>
                <div class="mb-2.5">
                    <x-input-label value="Date Added" />
                    <x-modal-input id="archive-date-added" placeholder="Date Added" readonly />
                </div>
                <div class="mb-2.5">
                    <x-input-label value="Last Updated By" />
                    <x-modal-input id="archive-last-updated-by" placeholder="Last Updated By" readonly />
                </div>
            </div>
        </div>

        <!-- File Section -->
        <div class="flex basis-3/4 flex-col items-center">
            <!-- File Preview View -->
            <div class="flex h-full w-full flex-col rounded-lg border border-gray-300 bg-gray-100">
                <div class="flex items-center justify-between bg-gray-50 p-0.5">
                    <div class="flex-1 flex items-center justify-center">
                        <input class="w-full border-none bg-transparent text-center text-lg font-bold text-gray-800 outline-none"
                            id="archive-file-label-preview" type="text" readonly />
                    </div>
                    <button class="text-blue-600 hover:text-blue-800" id="export-archive-btn" title="Export File" onclick="exportArchiveFile()">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                </div>
                <div class="w-full flex-1 h-[90%]" id="archive-file-preview-container">
                    <iframe class="h-full w-full" id="archive-file-preview" style="display: none;"></iframe>
                    <div class="flex h-full items-center justify-center text-gray-500" id="archive-file-placeholder">No file selected</div>
                </div>
            </div>
        </div>
    </div>
</x-modal>
