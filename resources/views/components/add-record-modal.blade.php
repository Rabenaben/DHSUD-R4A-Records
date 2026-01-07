<!-- Add Record Modal -->
<x-modal name="add-record" maxWidth=".25 xl">
    <div class="p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Add New HOA Docket</h2>

        <form id="add-record-form" class="space-y-6">
            <!-- Basic Information -->
            <div>
                <h3 class="mb-3 text-sm font-semibold text-gray-700 border-b border-gray-200 pb-2">Docket Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Docket No." />
                        <x-text-input id="add-docket-no" name="docket_no" required />
                    </div>
                    <div>
                        <x-input-label value="HOA Name" />
                        <x-text-input id="add-hoa-name" name="hoa_name" required />
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div>
                <h3 class="mb-3 text-sm font-semibold text-gray-700 border-b border-gray-200 pb-2">Location</h3>
                <div class="space-y-4">
                    <div>
                        <x-input-label value="Location" />
                        <x-text-input id="add-location" name="location" required />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Province" />
                            <select id="add-province" name="province_id" class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                                <option value="">Select Province</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->province_id }}">{{ $province->province_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label value="Municipality" />
                            <select id="add-municipality" name="municipality_id" class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required disabled>
                                <option value="">Select Municipality</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div>
                <h3 class="mb-3 text-sm font-semibold text-gray-700 border-b border-gray-200 pb-2">Additional Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Status" />
                        <select id="add-status" name="status" class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                            <option value="ON-SHELF">ON-SHELF</option>
                            <option value="BORROWED">BORROWED</option>
                            <option value="UNAVAILABLE">UNAVAILABLE</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Quantity" />
                        <x-text-input id="add-quantity" name="quantity" type="number" required />
                    </div>
                </div>
                <div class="mt-4">
                    <x-input-label value="Remarks" />
                    <textarea id="add-remarks" name="remarks" class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" rows="3" placeholder="Enter any additional remarks..."></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" id="cancel-add-record-btn"
                    class="rounded-lg bg-gray-500 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-600">
                    Cancel
                </button>
                <button type="button" id="add-record-submit-btn"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Add Docket
                </button>
            </div>
        </form>
    </div>
</x-modal>
