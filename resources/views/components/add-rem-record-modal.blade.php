<!-- Add REM Record Modal -->
<x-modal name="add-rem-record" maxWidth=".25 xl">
    <div class="p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Add New REM Docket</h2>

        <form id="add-rem-record-form" class="space-y-6">
            <!-- Basic Information -->
            <div>
                <h3 class="mb-3 text-sm font-semibold text-gray-700 border-b border-gray-200 pb-2">Docket Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Docket No." />
                        <x-text-input id="add-rem-docket-no" name="docket_no" required />
                    </div>
                    <div>
                        <x-input-label value="Project Name" />
                        <x-text-input id="add-rem-project-name" name="project_name" required />
                    </div>
                </div>
            </div>

            <!-- Province -->
            <div class="province-section">
                <h3 class="mb-3 text-sm font-semibold text-gray-700 border-b border-gray-200 pb-2">Province</h3>
                <div>
                    <x-input-label value="Province" />
                    <select id="add-rem-province" name="province" class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                        <option value="">Select Province</option>
                        @foreach($provinces as $province)
                            <option value="{{ is_object($province) ? $province->province_name : $province }}">{{ is_object($province) ? $province->province_name : $province }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Additional Information -->
            <div>
                <h3 class="mb-3 text-sm font-semibold text-gray-700 border-b border-gray-200 pb-2">Additional Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Status" />
                        <select id="add-rem-status" name="status" class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                            <option value="ON-SHELF">ON-SHELF</option>
                            <option value="BORROWED">BORROWED</option>
                            <option value="UNAVAILABLE">UNAVAILABLE</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Quantity" />
                        <x-text-input id="add-rem-quantity" name="quantity" type="number" required />
                    </div>
                </div>
                <div class="mt-4">
                    <x-input-label value="Remarks" />
                    <textarea id="add-rem-remarks" name="remarks" class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" rows="3" placeholder="Enter any additional remarks..."></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" id="cancel-add-rem-record-btn"
                    class="rounded-lg bg-gray-500 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-600">
                    Cancel
                </button>
                <button type="button" id="add-rem-record-submit-btn"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Add Docket
                </button>
            </div>
        </form>
    </div>
</x-modal>
