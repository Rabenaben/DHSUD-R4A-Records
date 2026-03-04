<!-- Add Client Request Form Modal -->
<x-modal name="add-client-request" maxWidth="2xl">
    <div class="p-6 max-h-screen overflow-y-auto">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Add Client Request Form</h2>
        <form id="add-client-request-form" class="space-y-6">
            <!-- Date and Type -->
            <div>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label value="Date" :required="true" />
                        <x-text-input id="request-date" name="date" type="date" class="w-full" required />
                    </div>
                    <div>
                        <x-input-label value="Type" :required="true" />
                        <select id="request-type" name="type"
                            class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                            required>
                            <option value="">Select Type</option>
                            <option value="HOA">HOA</option>
                            <option value="REM">REM</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Project Information -->
            <div>
                <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Project Information</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label value="Name of Project / HOA" :required="true" />
                        <x-text-input id="project-name" name="project_name" class="w-full" required />
                    </div>
                    <div>
                        <x-input-label value="Docket No." :required="true" />
                        <x-text-input id="docket-no" name="docket_no" class="w-full" required />
                    </div>
                </div>
                <div class="mt-4">
                    <x-input-label value="Location" />
                    <x-text-input id="location" name="location" class="w-full" />
                </div>
            </div>

            <!-- Request Information -->
            <div>
                <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Request Information</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label value="Requested By" :required="true" />
                        <x-text-input id="requested-by" name="requested_by" class="w-full" required />
                    </div>
                    <div>
                        <x-input-label value="OR No." :required="true" />
                        <x-text-input id="or-no" name="or_no" class="w-full" required />
                    </div>
                </div>
                <div class="mt-4">
                    <x-input-label value="Amount" :required="true" />
                    <x-text-input id="amount" name="amount" type="number" step="0.01" class="w-full" required />
                </div>
            </div>

            <!-- Requested Documents -->
            <div>
                <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Requested Documents</h3>
                <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                    @php
                        $documents = [
                            'Certificate of Incorporation',
                            'Certificate of Amended By-Laws',
                            'Certificate of Amended Articles of Incorporation',
                            'Articles of Incorporation',
                            'By-Laws',
                            'Annual Report',
                            'Election Report'
                        ];
                    @endphp
                    @foreach($documents as $doc)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="requested_docs[]" value="{{ $doc }}"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">{{ $doc }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Remarks -->
            <div>
                <x-input-label value="Remarks" />
                <textarea id="remarks" name="remarks"
                    class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    rows="3" placeholder="Enter any additional remarks..."></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" id="cancel-add-client-request-btn"
                    class="rounded-lg bg-gray-500 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-600">
                    Cancel
                </button>
                <button type="button" id="add-client-request-submit-btn"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</x-modal>
