<!-- HOA Modal -->
<x-modal name="hoa" maxWidth="7xl">
    <button
        class="ml-2 mt-2 flex items-center gap-2 rounded-lg bg-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
        onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'hoa' } }))">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        Close
    </button>
    <div class="flex flex-col gap-6 p-4 lg:flex-row min-h-[90vh] max-h-[90vh] overflow-hidden">

        <!-- Form Section -->
        <div class="flex-1/4 overflow-y-auto">
            <!-- Basic Information -->
            <h3 class="mb-2 mt-4 flex items-center justify-between text-[15px] font-semibold">
                Basic Information
                @unless(auth()->user()->role === 'Staff')
                <div class="flex items-center space-x-2" id="hoa-edit-icons" style="display: none;">
                    <button class="text-green-600 hover:text-green-800" id="hoa-save-icon" title="Save Changes">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </button>
                    <button class="text-red-600 hover:text-red-800" id="hoa-cancel-icon" title="Cancel Changes">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <button class="rounded-lg bg-green-600 px-3 py-1 text-sm font-semibold text-white hover:bg-green-700"
                    id="hoa-edit-btn">EDIT</button>
                @endunless
            </h3>
            <div class="mb-2.5">
                <div class="mb-2.5">
                    <x-input-label value="Region" />
                    <x-modal-input id="region" placeholder="Region" readonly />
                </div>
                <div class="mb-2.5">
                    <x-input-label value="Docket No." />
                    <x-modal-input id="docket-no" placeholder="Docket No." readonly />
                </div>
                <div class="mb-2.5">
                    <x-input-label value="HOA Name" />
                    <x-modal-input id="hoa-name" placeholder="HOA Name" readonly />
                </div>
                <div class="mb-2.5">
                    <x-input-label value="Classification" />
                    <x-modal-input id="classification" placeholder="Classification" readonly />
                </div>
                <div class="mb-2.5">
                    <x-input-label value="HOA Status" />
                    <select class="w-full rounded-lg border border-gray-300 p-2 outline-none focus:border-blue-600" id="hoa-status" disabled>
                        <option value="REGISTERED">REGISTERED</option>
                        <option value="NOT REGISTERED">NOT REGISTERED</option>
                        <option value="DENIED">DENIED</option>
                        <option value="SUSPENDED">SUSPENDED</option>
                        <option value="REVOKED/CANCELLED">REVOKED/CANCELLED</option>
                        <option value="DISSOLVED">DISSOLVED</option>
                    </select>
                </div>
            </div>

            <!-- Location -->
            <h3 class="mb-2 mt-4 text-[15px] font-semibold">Location</h3>
            <div class="mb-2.5">
                <x-input-label value="Location" />
                <x-modal-input id="location" placeholder="Location" readonly />
            </div>
            <div class="mb-2.5 flex gap-2.5">
                <div class="flex-1">
                    <x-input-label value="Province" />
                    <x-modal-input id="province" placeholder="Province" readonly />
                </div>
                <div class="flex-1">
                    <x-input-label value="Municipality" />
                    <x-modal-input id="municipality" placeholder="Municipality" readonly />
                </div>
            </div>

            <!-- Additional Information -->
            <h3 class="mb-2 mt-4 text-[15px] font-semibold">Additional Information</h3>
            <div class="mb-2.5 flex gap-2.5">
                <div class="flex-1">
                    <x-input-label value="Status" />
                    <select class="w-full rounded-lg border border-gray-300 p-2 outline-none focus:border-blue-600"
                        id="status" disabled>
                        <option value="ON-SHELF">ON-SHELF</option>
                        <option value="UNAVAILABLE">UNAVAILABLE</option>
                    </select>
                </div>
                <div class="flex-1">
                    <x-input-label value="Quantity" />
                    <x-modal-input id="quantity" placeholder="Quantity" readonly />
                </div>
            </div>

            <div>
                <x-input-label value="Remarks" />
                <textarea
                    class="min-h-[50px] w-full resize-none rounded-lg border border-gray-300 p-2 outline-none focus:border-blue-600"
                    id="remarks" placeholder="Remarks" readonly></textarea>
            </div>
        </div>

        <!-- File Section -->
        <div class="flex basis-3/4 flex-col items-center">
            <div class="mb-4 mt-2 text-center text-lg font-bold text-gray-800" id="hoa-file-label"></div>
            <!-- File List View -->
            <div class="flex-1 w-full overflow-hidden rounded-lg border border-gray-300 bg-white"
                id="hoa-file-list-view" style="display: block;">
                <div class="mb-2 flex items-center justify-between bg-gray-50 p-4">
                    <h4 class="text-sm font-semibold text-gray-900">Files</h4>
                    <div class="flex items-center space-x-2">
                        @unless(auth()->user()->role === 'Staff')
                        <x-secondary-button id="hoa-add-file-btn">Add File</x-secondary-button>
                        @endunless
                    </div>
                </div>
                <div class="flex h-full justify-center overflow-x-auto overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="w-1/3 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                    File Name</th>
                                <th
                                    class="w-1/3 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Date Modified</th>
                                <th
                                    class="w-1/3 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Last Updated By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y bg-white" id="hoa-file-list-body">
                            {{-- Files will be rendered here via JS --}}
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- File Preview View -->
            <div class="flex h-full w-full flex-col rounded-lg border border-gray-300 bg-gray-100"
                id="hoa-file-preview-view" style="display: none;">
                <div class="flex items-center p-4">
                    <button
                        class="flex items-center gap-2 rounded-lg bg-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
                        onclick="hoaShowFileList()">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Back to Files
                    </button>
                    <div class="ml-4 flex-1 flex items-center justify-center">
                        <input class="w-full border-none bg-transparent text-center text-lg font-bold text-gray-800 outline-none"
                            id="hoa-file-label-preview" type="text" readonly />
                        @unless(auth()->user()->role === 'Staff')
                        <div class="ml-2 flex items-center space-x-2" id="hoa-file-edit-actions">
                            <button class="text-gray-600 hover:text-gray-800" id="hoa-edit-file-name-btn"
                                title="Edit File Name">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </button>
                            <button class="text-blue-600 hover:text-blue-800" id="export-hoa-btn" title="Export File" onclick="exportHoaFile()">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </button>
                            <button class="text-red-600 hover:text-red-800" id="archive-hoa-btn" title="Archive File">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="ml-2 flex items-center space-x-2" id="hoa-file-name-save-icons"
                            style="display: none;">
                            <button class="text-green-600 hover:text-green-800" id="hoa-save-file-name-icon"
                                title="Save File Name">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                            <button class="text-red-600 hover:text-red-800" id="hoa-cancel-file-name-icon"
                                title="Cancel File Name Edit">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        @endunless
                    </div>
                </div>
                <div class="w-full flex-1 h-4/5" id="file-preview-container">
                    <iframe class="h-full w-full" id="file-preview" style="display: none;"></iframe>
                    <div class="flex h-full items-center justify-center text-gray-500" id="file-placeholder">No file
                        selected</div>
                </div>
            </div>
        </div>
    </div>
</x-modal>
