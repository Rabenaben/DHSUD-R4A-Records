@props(['type' => 'hoa', 'provinces' => []])

<!-- Add Record Modal -->
<x-modal name="{{ $type === 'hoa' ? 'add-record' : 'add-rem-record' }}" maxWidth=".25 xl">
    <div class="p-6 {{ $type === 'hoa' ? 'max-h-screen overflow-y-auto' : '' }}">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Add New {{ strtoupper($type) }} Docket</h2>

        <form id="{{ $type === 'hoa' ? 'add-record-form' : 'add-rem-record-form' }}" class="space-y-6">
            <!-- Basic Information -->
            <div>
                <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Docket Information
                </h3>
                @if ($type === 'hoa')
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <x-input-label value="HOA ID" :required="true" />
                            <x-text-input id="add-hoa-id" name="hoa_id" type="number" required />
                        </div>
                        <div>
                            <x-input-label value="Docket No." :required="true" />
                            <x-text-input id="add-docket-no" name="docket_no" required />
                        </div>
                        <div>
                            <x-input-label value="HOA Name" :required="true" />
                            <x-text-input id="add-hoa-name" name="hoa_name" required />
                        </div>
                        <div>
                            <x-input-label value="Classification" :required="true" />
                            <x-text-input id="add-classification" name="classification" required />
                        </div>
                        <div>
                            <x-input-label value="HOA Status" :required="true" />
                            <select id="add-hoa-status" name="hoa_status"
                                class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">Select HOA Status</option>
                                <option value="REGISTERED">REGISTERED</option>
                                <option value="NOT REGISTERED">NOT REGISTERED</option>
                                <option value="DENIED">DENIED</option>
                                <option value="SUSPENDED">SUSPENDED</option>
                                <option value="REVOKED/CANCELLED">REVOKED/CANCELLED</option>
                                <option value="DISSOLVED">DISSOLVED</option>
                            </select>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label value="Docket No." :required="true" />
                            <x-text-input id="add-rem-docket-no" name="docket_no" class="w-full" required />
                        </div>
                        <div>
                            <x-input-label value="Project Name" :required="true" />
                            <x-text-input id="add-rem-project-name" name="project_name" class="w-full" required />
                        </div>
                    </div>
                @endif
            </div>

            @if ($type === 'hoa')
                <!-- Location -->
                <div>
                    <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Location</h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <x-input-label value="Location" :required="true" />
                            <x-text-input id="add-location" name="location" required />
                        </div>
                        <div>
                            <x-input-label value="Province" :required="true" />
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
                            <x-input-label value="Municipality" :required="true" />
                            <select id="add-municipality" name="municipality_id"
                                class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                required disabled>
                                <option value="">Select Municipality</option>
                            </select>
                        </div>
                    </div>
                </div>
            @else
                <!-- Location -->
                <div>
                    <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Location</h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <x-input-label value="Location" :required="true" />
                            <x-text-input id="add-rem-location" name="location" class="w-full" required />
                        </div>
                        <div>
                            <x-input-label value="Province" :required="true" />
                            <select id="add-rem-province" name="province_id"
                                class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">Select Province</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ is_object($province) ? $province->province_id : $province }}">
                                        {{ is_object($province) ? $province->province_name : $province }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label value="Municipality" :required="true" />
                            <select id="add-rem-municipality" name="municipality_id"
                                class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                required disabled>
                                <option value="">Select Municipality</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Additional Information -->
            <div>
                <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Additional
                    Information</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label value="Status" />
                        <select id="{{ $type === 'hoa' ? 'add-status' : 'add-rem-status' }}" name="status" class="w-full rounded-lg border border-gray-300 p-2">
                            <option value="ON-SHELF" selected>ON-SHELF</option>
                            <option value="UNAVAILABLE">UNAVAILABLE</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Quantity" :required="true" />
                        <x-text-input id="{{ $type === 'hoa' ? 'add-quantity' : 'add-rem-quantity' }}" name="quantity"
                            type="number" class="w-full" required />
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
