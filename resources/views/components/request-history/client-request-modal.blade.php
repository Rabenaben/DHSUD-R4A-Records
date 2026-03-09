<!-- Add/View Client Request Form Modal -->
<x-modal name="client-request-modal" maxWidth="2xl">
    <div class="max-h-screen overflow-y-auto p-6">
        <!-- Header -->
        <h2 class="mb-4 text-lg font-semibold text-gray-900" id="client-request-modal-title">Add Client Request Form</h2>

        <form class="space-y-6" id="client-request-form">
            <!-- Hidden input to store request ID for edit mode -->
            <input type="hidden" id="client-request-id" name="id" value="">

            <!-- Mode indicator -->
            <input type="hidden" id="client-request-mode" name="mode" value="add">

            <!-- Date and Type -->
            <div>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label value="Date" :required="true" />
                        <x-text-input class="w-full" id="request-date" name="date" type="date" required />
                    </div>
                    <div>
                        <x-input-label value="Type" :required="true" />
                        <select
                            class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                            id="request-type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="HOA">HOA</option>
                            <option value="REM">REM</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Project Information -->
            <div>
                <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Project Information
                </h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label value="Docket No." :required="true" />
                        <x-text-input class="w-full" id="docket-no" name="docket_no" maxlength="50" required />
                    </div>
                    <div>
                        <x-input-label value="Name of Project / HOA" :required="true" />
                        <x-text-input class="w-full" id="project-name" name="project_name" maxlength="255" required />
                    </div>
                </div>
                <div class="mt-4">
                    <x-input-label value="Location" />
                    <x-text-input class="w-full" id="location" name="location" maxlength="255" />
                </div>
            </div>

            <!-- Request Information -->
            <div>
                <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Request Information
                </h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label value="Requested By" :required="true" />
                        <x-text-input class="w-full" id="requested-by" name="requested_by" maxlength="100" required />
                    </div>
                    <div>
                        <x-input-label value="OR No." :required="true" />
                        <x-text-input class="w-full" id="or-no" name="or_no" maxlength="50" required />
                    </div>
                </div>
                <div class="mt-4">
                    <x-input-label value="Amount" :required="true" />
                    <x-text-input class="w-full" id="amount" name="amount" type="number" step="0.01"
                        min="0.01" required />
                </div>
            </div>

            <!-- Requested Documents -->
            <div>
                <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Requested Documents
                </h3>
                <div class="grid grid-cols-1 gap-2 md:grid-cols-2" id="requested-docs-container">
                    <!-- Documents will be dynamically rendered based on Type selection -->
                    <p class="text-sm text-gray-500" id="select-type-message">Please select a Type to see available documents.</p>
                </div>

                <!-- Others input field (hidden by default) -->
                <div id="others-input-container" class="mt-3 hidden">
                    <x-input-label value="Please specify:" />
                    <x-text-input class="w-full" id="others-document-specify" name="others_specify"
                        maxlength="255" placeholder="Specify the document..." />
                </div>

                <!-- View mode documents display -->
                <div id="requested-docs-view" class="hidden">
                    <div class="flex flex-wrap gap-2" id="requested-docs-list"></div>
                </div>
            </div>

            <!-- Certified True Copy Option -->
            <div id="certified-true-copy-section" class="hidden">
                <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Certification Option
                </h3>
                <div class="mt-2 flex gap-4">
                    <label class="flex items-center space-x-2">
                        <input
                            type="radio"
                            id="certified-true-copy"
                            name="certification_status"
                            value="certified"
                            class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                        >
                        <span class="text-sm text-gray-700">Certified</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input
                            type="radio"
                            id="not-certified"
                            name="certification_status"
                            value="not_certified"
                            class="rounded border-gray-300 text-red-600 focus:ring-red-500"
                        >
                        <span class="text-sm text-gray-700">Not Certified</span>
                    </label>
                </div>

                <!-- View mode Certified/Not Certified display -->
                <div id="certified-true-copy-view" class="mt-3 hidden">
                    <span id="certification-badge" class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">Certified</span>
                </div>
            </div>

            <!-- Remarks -->
            <div>
                <x-input-label value="Remarks" />
                <textarea
                    class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    id="remarks" name="remarks" rows="3" maxlength="1000" placeholder="Enter any additional remarks..."></textarea>
            </div>

            <!-- View Mode Only: Additional Info -->
            <div id="view-mode-info" class="hidden rounded-lg bg-gray-50 p-4">
                <h4 class="mb-3 text-sm font-semibold text-gray-700">Additional Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-500">Created At:</span>
                        <span id="created-at" class="text-gray-900"></span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-500">Updated At:</span>
                        <span id="updated-at" class="text-gray-900"></span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3" id="form-buttons">
                <button class="rounded-lg bg-gray-500 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-600"
                    id="cancel-client-request-btn" type="button">
                    Cancel
                </button>
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                    id="client-request-submit-btn" type="button">
                    Submit Request
                </button>
            </div>

            <!-- View Mode Buttons -->
            <div class="flex justify-end gap-3" id="view-mode-buttons">
                <button class="rounded-lg bg-gray-500 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-600"
                    id="close-view-btn" type="button">
                    Close
                </button>
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                    id="edit-request-btn" type="button">
                    Edit
                </button>
            </div>
        </form>
    </div>
</x-modal>
