@props(['provinces' => []])

<!-- REM Modal -->
<x-modal name="rem" maxWidth="7xl">
    <button
        class="ml-2 mt-2 flex items-center gap-2 rounded-lg bg-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
        onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'rem' } }))">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        Close
    </button>
    <div class="flex flex-col gap-6 p-4 lg:flex-row min-h-[90vh] max-h-[90vh] overflow-hidden">

        <!-- Form Section -->
        <div class="basis-1/4 overflow-y-auto">
            <!-- Basic Information -->
            <h3 class="mb-2 mt-4 flex items-center justify-between text-[15px] font-semibold">
                Basic Information
                @unless(auth()->user()->role === 'Staff')
                <div class="flex items-center space-x-2" id="rem-edit-icons" style="display: none;">
                    <button class="text-green-600 hover:text-green-800" id="rem-save-icon" title="Save Changes">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </button>
                    <button class="text-red-600 hover:text-red-800" id="rem-cancel-icon" title="Cancel Changes">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <button class="rounded-lg bg-green-600 px-3 py-1 text-sm font-semibold text-white hover:bg-green-700"
                    id="rem-edit-btn">EDIT</button>
                @endunless
            </h3>
            <div class="mb-2.5 flex gap-2.5">
                <div class="flex-1">
                    <x-input-label for="rem-docket-no" value="Docket No." required :class="'required-label'" />
                    <x-modal-input id="rem-docket-no" placeholder="Docket No." readonly />
                </div>
                <div class="flex-1">
                    <x-input-label for="rem-project-name" value="Project Name" required :class="'required-label'" />
                    <x-modal-input id="rem-project-name" placeholder="Project Name" readonly />
                </div>
            </div>

            <!-- Location -->
            <h3 class="mb-2 mt-4 text-[15px] font-semibold">Location</h3>
            <div class="mb-2.5">
                <x-input-label for="rem-location" value="Location" required :class="'required-label'" />
                <x-modal-input id="rem-location" placeholder="Location" readonly />
            </div>

            <div class="mb-2.5 flex gap-2.5">
                <div class="flex-1">
                    <x-input-label for="rem-province" value="Province" required :class="'required-label'" />
                    <select id="rem-province" 
                        class="w-full rounded-lg border border-gray-300 p-2 outline-none focus:border-blue-600"
                        disabled>
                        <option value="">Select Province</option>
                        @foreach ($provinces as $province)
                            <option value="{{ is_object($province) ? $province->province_id : $province }}">
                                {{ is_object($province) ? $province->province_name : $province }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <x-input-label for="rem-municipality" value="Municipality" required :class="'required-label'" />
                    <select id="rem-municipality" 
                        class="w-full rounded-lg border border-gray-300 p-2 outline-none focus:border-blue-600"
                        disabled>
                        <option value="">Select Municipality</option>
                    </select>
                </div>
            </div>

            <!-- Additional Information -->
            <h3 class="mb-2 mt-4 text-[15px] font-semibold">Additional Information</h3>
            <div class="mb-2.5 flex gap-2.5">
                <div class="flex-1">
                    <x-input-label for="rem-status" value="Status" required :class="'required-label'" />
                    <select class="w-full rounded-lg border border-gray-300 p-2 outline-none focus:border-blue-600"
                        id="rem-status" disabled>
                        <option value="ON-SHELF">ON-SHELF</option>
                        <option value="UNAVAILABLE">UNAVAILABLE</option>
                    </select>
                </div>
                <div class="flex-1">
                    <x-input-label for="rem-quantity" value="Quantity" required :class="'required-label'" />
                    <x-modal-input id="rem-quantity" placeholder="Quantity" readonly />
                </div>
            </div>

            <div>
                <x-input-label for="rem-remarks" value="Remarks" />
                <textarea
                    class="min-h-[50px] w-full resize-none rounded-lg border border-gray-300 p-2 outline-none focus:border-blue-600"
                    id="rem-remarks" placeholder="Remarks" readonly></textarea>
            </div>
        </div>

        <!-- File Section -->
        <div class="flex basis-3/4 flex-col items-center">
            <div class="mb-4 mt-2 text-center text-lg font-bold text-gray-800" id="rem-file-label"></div>
            <!-- File List View -->
            <div class="flex-1 w-full overflow-hidden rounded-lg border border-gray-300 bg-white"
                id="rem-file-list-view" style="display: block;">
                <div class="mb-2 flex items-center justify-between bg-gray-50 p-4">
                    <div class="flex items-center gap-3">
                        <h4 class="text-sm font-semibold text-gray-900">Files</h4>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="rem-files-search"
                                placeholder="Search files..." 
                                class="w-48 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                            <button 
                                id="rem-files-search-clear"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-0.5"
                                style="display: none;"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @unless(auth()->user()->role === 'Staff')
                        <x-secondary-button id="rem-add-file-btn">Add File</x-secondary-button>
                        <x-secondary-button id="rem-export-all-files-btn" class="bg-green-600! text-white! hover:bg-green-700! opacity-50 cursor-not-allowed" disabled>
                            Export All Files
                        </x-secondary-button>
                        @endunless
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
                                    Date Modified</th>
                                <th
                                    class="w-2/5 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Last Updated By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white max-h-[20vh] overflow-y-auto" id="rem-file-list-body">
                            {{-- Files will be rendered here via JS --}}
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- File Preview View -->
            <div class="flex h-full w-full flex-col rounded-lg border border-gray-300 bg-gray-100"
                id="rem-file-preview-view" style="display: none;">
                <div class="flex items-center p-1">
                    <button
                        class="flex items-center gap-2 rounded-lg bg-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
                        onclick="remShowFileList()">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Back to Files
                    </button>
                    <div class="ml-4 flex-1 flex items-center justify-center">
                        <input
                            class="w-full border-none bg-transparent text-center text-lg font-bold text-gray-800 outline-none"
                            id="rem-file-label-preview" type="text" readonly />
                        @unless(auth()->user()->role === 'Staff')
                        <div class="ml-2 flex items-center space-x-2" id="rem-file-edit-actions">
                            <button class="text-gray-600 hover:text-gray-800" id="rem-edit-file-name-btn"
                                title="Edit File Name">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </button>
                            <button class="text-blue-600 hover:text-blue-800" id="export-rem-btn" title="Export File" onclick="exportRemFile()">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </button>
                            <button class="text-red-600 hover:text-red-800" id="archive-rem-btn" title="Archive File">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="ml-2 flex items-center space-x-2" id="rem-file-name-save-icons"
                            style="display: none;">
                            <button class="text-green-600 hover:text-green-800" id="rem-save-file-name-icon"
                                title="Save File Name">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                            <button class="text-red-600 hover:text-red-800" id="rem-cancel-file-name-icon"
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
                <div class="w-full flex-1 h-[90%]" id="rem-file-preview-container">
                    <iframe class="h-full w-full" id="rem-file-preview" style="display: none;"></iframe>
                    <div class="flex h-full items-center justify-center text-gray-500" id="rem-file-placeholder">No
                        file
                        selected</div>
                </div>
            </div>
        </div>
    </div>
</x-modal>
