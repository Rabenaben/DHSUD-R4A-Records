<!-- REM Modal -->
<x-modal name="rem" maxWidth="6xl">
    <button
        class="ml-2 mt-2 flex items-center gap-2 rounded-lg bg-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
        onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'rem' } }))">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        Close
    </button>
    <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-2">

        <!-- Form Section -->
        <div class="flex-1">
            <!-- Basic Information -->
            <h3 class="mb-2 mt-4 text-[15px] font-semibold">Basic Information</h3>
            <div class="mb-2.5 flex gap-2.5">
                <div class="flex-1">
                    <x-input-label value="Docket No." />
                    <x-modal-input id="rem-docket-no" placeholder="Docket No." readonly />
                </div>
                <div class="flex-1">
                    <x-input-label value="Project Name" />
                    <x-modal-input id="rem-project-name" placeholder="Project Name" readonly />
                </div>
            </div>

            <!-- Location -->
            <h3 class="mb-2 mt-4 text-[15px] font-semibold">Location</h3>
            <div class="mb-2.5 flex gap-2.5">
                <div class="flex-1">
                    <x-input-label value="Province" />
                    <x-modal-input id="rem-province" placeholder="Province" readonly />
                </div>
            </div>

            <!-- Additional Information -->
            <h3 class="mb-2 mt-4 text-[15px] font-semibold">Additional Information</h3>
            <div class="mb-2.5 flex gap-2.5">
                <div class="flex-1">
                    <x-input-label value="Status" />
                    <select id="rem-status" disabled class="w-full rounded-lg border border-gray-300 bg-gray-100 p-2 outline-none">
                        <option value="ON-SHELF">ON-SHELF</option>
                        <option value="UNAVAILABLE">UNAVAILABLE</option>
                    </select>
                </div>
                <div class="flex-1">
                    <x-input-label value="Quantity" />
                    <x-modal-input id="rem-quantity" placeholder="Quantity" readonly />
                </div>
            </div>

            <div>
                <x-input-label value="Remarks" />
                <textarea
                    class="min-h-[50px] w-full resize-none rounded-lg border border-gray-300 p-2 outline-none focus:border-blue-600"
                    id="rem-remarks" placeholder="Remarks" readonly></textarea>
            </div>

            <div id="rem-file-name-field" style="display: none;">
                <x-input-label value="File Name" />
                <x-modal-input id="rem-file-name" placeholder="File Name" readonly />
            </div>
        </div>

        <!-- File Section -->
        <div class="flex flex-1 flex-col items-center">
            <!-- File List View -->
            <div id="rem-file-list-view" class="h-[340px] w-[90%] rounded-lg border border-gray-300 bg-white overflow-hidden" style="display: block;">
                <div class="mb-2 flex items-center justify-between p-4 bg-gray-50">
                    <h4 class="text-sm font-semibold text-gray-900">Files</h4>
                    <div class="flex items-center space-x-2">
                        <x-secondary-button id="rem-add-file-btn">Add File</x-secondary-button>
                    </div>
                </div>
                <div class="overflow-x-auto overflow-y-auto max-h-72 flex justify-center">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500 w-1/3">
                                    File Name</th>
                                <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500 w-1/3">
                                    Date Modified</th>
                                <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500 w-1/3">
                                    Last Updated By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white" id="rem-file-list-body">
                            {{-- Files will be rendered here via JS --}}
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- File Preview View -->
            <div id="rem-file-preview-view" class="h-[340px] w-[90%] rounded-lg border border-gray-300 bg-gray-100" style="display: none;">
                <button
                    class="ml-2 mt-2 flex items-center gap-2 rounded-lg bg-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
                    onclick="remShowFileList()">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Files
                </button>
                <div class="h-[300px] w-full" id="rem-file-preview-container">
                    <iframe id="rem-file-preview" class="w-full h-full" style="display: none;"></iframe>
                    <div id="rem-file-placeholder" class="flex items-center justify-center h-full text-gray-500">No file selected</div>
                </div>
            </div>

            <div class="mb-4 mt-2 text-sm font-medium text-gray-800" id="rem-file-label"></div>
            <div class="flex gap-3" id="rem-file-actions" style="display: none;">
                <button id="rem-edit-btn" class="rounded-lg bg-green-600 px-6 py-2 font-semibold text-white hover:bg-green-700">EDIT</button>
                <button id="rem-save-btn" class="rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white hover:bg-blue-700" style="display: none;">SAVE</button>
                <button id="rem-cancel-btn" class="rounded-lg bg-gray-600 px-6 py-2 font-semibold text-white hover:bg-gray-700" style="display: none;">CANCEL</button>
                <button id="export-rem-btn" onclick="exportRemFile()" class="rounded-lg bg-blue-800 px-6 py-2 font-semibold text-white hover:bg-blue-900">EXPORT
                    FILE</button>
                <button id="archive-rem-btn" class="rounded-lg bg-red-600 px-6 py-2 font-semibold text-white hover:bg-red-700">ARCHIVE
                    FILE</button>
            </div>
        </div>
    </div>
</x-modal>
