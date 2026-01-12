@props(['type' => 'hoa', 'provinces' => []])

<!-- Add Record Modal -->
<x-modal name="{{ $type === 'hoa' ? 'add-record' : 'add-rem-record' }}" maxWidth=".25 xl">
    <div class="p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Add New {{ strtoupper($type) }} Docket</h2>

        <form id="{{ $type === 'hoa' ? 'add-record-form' : 'add-rem-record-form' }}" class="space-y-6">
            <!-- Basic Information -->
            <div>
                <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Docket Information
                </h3>
                @if ($type === 'hoa')
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <x-input-label value="Region<span style='color: red;'>*</span>" />
                            <select id="add-region" name="region"
                                class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">Select Region</option>
                                <option value="RIV">RIV</option>
                                <option value="STR">STR</option>
                                <option value="RIZAL">RIZAL</option>
                                <option value="CALABARZON">CALABARZON</option>
                                <option value="NCR HOA">NCR HOA</option>
                                <option value="NCR HOA N">NCR HOA N</option>
                                <option value="R4A">R4A</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label value="Docket No.<span style='color: red;'>*</span>" />
                            <x-text-input id="add-docket-no" name="docket_no" required />
                        </div>
                        <div>
                            <x-input-label value="HOA Name<span style='color: red;'>*</span>" />
                            <x-text-input id="add-hoa-name" name="hoa_name" required />
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label value="Docket No.<span style='color: red;'>*</span>" />
                            <x-text-input id="add-rem-docket-no" name="docket_no" required />
                        </div>
                        <div>
                            <x-input-label value="Project Name<span style='color: red;'>*</span>" />
                            <x-text-input id="add-rem-project-name" name="project_name" required />
                        </div>
                    </div>
                @endif
            </div>

            @if ($type === 'hoa')
                <!-- Location -->
                <div>
                    <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Location</h3>
                    <div class="space-y-4">
                        <div>
                            <x-input-label value="Location<span style='color: red;'>*</span>" />
                            <x-text-input id="add-location" name="location" required />
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label value="Province<span style='color: red;'>*</span>" />
                                <select id="add-province" name="province_id"
                                    class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                    required>
                                    <option value="">Select Province</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->province_id }}">{{ $province->province_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Municipality<span style='color: red;'>*</span>" />
                                <select id="add-municipality" name="municipality_id"
                                    class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                    required disabled>
                                    <option value="">Select Municipality</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Province -->
                <div class="province-section">
                    <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Province</h3>
                    <div>
                        <x-input-label value="Province<span style='color: red;'>*</span>" />
                        <select id="add-rem-province" name="province"
                            class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                            required>
                            <option value="">Select Province</option>
                            @foreach ($provinces as $province)
                                <option value="{{ is_object($province) ? $province->province_name : $province }}">
                                    {{ is_object($province) ? $province->province_name : $province }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <!-- Additional Information -->
            <div>
                <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Additional
                    Information</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label value="Status<span style='color: red;'>*</span>" />
                        <select id="{{ $type === 'hoa' ? 'add-status' : 'add-rem-status' }}" name="status"
                            class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                            required>
                            <option value="ON-SHELF">ON-SHELF</option>
                            <option value="UNAVAILABLE">UNAVAILABLE</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Quantity<span style='color: red;'>*</span>" />
                        <x-text-input id="{{ $type === 'hoa' ? 'add-quantity' : 'add-rem-quantity' }}" name="quantity"
                            type="number" required />
                    </div>
                </div>
                <div class="mt-4">
                    <x-input-label value="Remarks" />
                    <textarea id="{{ $type === 'hoa' ? 'add-remarks' : 'add-rem-remarks' }}" name="remarks"
                        class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                        rows="3" placeholder="Enter any additional remarks..."></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button"
                    id="{{ $type === 'hoa' ? 'cancel-add-record-btn' : 'cancel-add-rem-record-btn' }}"
                    class="rounded-lg bg-gray-500 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-600">
                    Cancel
                </button>
                <button type="button"
                    id="{{ $type === 'hoa' ? 'add-record-submit-btn' : 'add-rem-record-submit-btn' }}"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Add Docket
                </button>
            </div>
        </form>
    </div>
</x-modal>
