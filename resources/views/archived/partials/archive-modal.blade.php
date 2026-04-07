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
                    <x-input-label value="Number of Files Archived" />
                    <x-modal-input id="archive-file-name" placeholder="Number of Files Archived" readonly />
                </div>
                <div class="mb-2.5">
                    <x-input-label value="Last Archive Date" />
                    <x-modal-input id="archive-date-added" placeholder="Last Archive Date" readonly />
                </div>
                <div class="mb-2.5">
                    <x-input-label value="Last Updated By" />
                    <x-modal-input id="archive-last-updated-by" placeholder="Last Updated By" readonly />
                </div>
            </div>
        </div>

        <!-- File Section -->
        <div class="flex basis-3/4 flex-col items-center">
            <div class="mb-4 mt-2 text-center text-lg font-bold text-gray-800" id="archive-file-label"></div>
            <!-- File List View -->
            <div class="flex-1 w-full overflow-hidden rounded-lg border border-gray-300 bg-white"
                id="archive-file-list-view" style="display: block;">
                <div class="mb-2 flex items-center justify-between bg-gray-50 p-4">
                    <div class="flex items-center gap-3">
                        <h4 class="text-sm font-semibold text-gray-900">Archived Files</h4>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="archive-files-search"
                                placeholder="Search files..." 
                                class="w-48 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                            <button 
                                id="archive-files-search-clear"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-0.5"
                                style="display: none;"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="h-full w-full overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th
                                    class="w-2/5 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                    File Name</th>
                                <th
                                    class="w-1/5 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Date Archived</th>
                                <th
                                    class="w-2/5 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Last Updated By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white" id="archive-file-list-body">
                            {{-- Archived files will be rendered here via JS --}}
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- File Preview View -->
            <div class="flex h-full w-full flex-col rounded-lg border border-gray-300 bg-gray-100"
                id="archive-file-preview-view" style="display: none;">
                <div class="flex items-center p-1">
                    <button
                        class="flex items-center gap-2 rounded-lg bg-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
                        onclick="archiveShowFileList()">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Back to Files
                    </button>
                    <div class="ml-4 flex-1 flex items-center justify-center">
                        <input class="w-full border-none bg-transparent text-center text-lg font-bold text-gray-800 outline-none"
                            id="archive-file-label-preview" type="text" readonly />
                        <button class="text-blue-600 hover:text-blue-800 ml-2" id="export-archive-btn" title="Export File" onclick="exportArchiveFile()">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </button>
                        <button class="text-green-600 hover:text-green-800 ml-2" id="unarchive-archive-btn" title="Unarchive File" onclick="unarchiveArchiveFile()">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M3 7l9-5 9 5m0 0v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="w-full flex-1 h-[90%]" id="archive-file-preview-container">
                    <iframe class="h-full w-full" id="archive-file-preview" style="display: none;"></iframe>
                    <div class="flex h-full items-center justify-center text-gray-500" id="archive-file-placeholder">No file selected</div>
                </div>
            </div>
        </div>
    </div>
</x-modal>
