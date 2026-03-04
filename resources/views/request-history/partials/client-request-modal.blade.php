<!-- Add Client Request Form Modal -->
<x-modal name="add-client-request" maxWidth="2xl">
    <div class="max-h-screen overflow-y-auto p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Add Client Request Form</h2>
        <form class="space-y-6" id="add-client-request-form">
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
                <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                    @php
                        $documents = [
                            'Certificate of Incorporation',
                            'Certificate of Amended By-Laws',
                            'Certificate of Amended Articles of Incorporation',
                            'Articles of Incorporation',
                            'By-Laws',
                            'Annual Report',
                            'Election Report',
                        ];
                    @endphp
                    @foreach ($documents as $doc)
                        <label class="flex items-center space-x-2">
                            <input class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" type="checkbox"
                                name="requested_docs[]" value="{{ $doc }}">
                            <span class="text-sm text-gray-700">{{ $doc }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Remarks -->
            <div>
                <x-input-label value="Remarks" />
                <textarea
                    class="w-full rounded-lg border border-gray-300 p-2 transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    id="remarks" name="remarks" rows="3" maxlength="1000" placeholder="Enter any additional remarks..."></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button class="rounded-lg bg-gray-500 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-600"
                    id="cancel-add-client-request-btn" type="button">
                    Cancel
                </button>
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                    id="add-client-request-submit-btn" type="button">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</x-modal>
