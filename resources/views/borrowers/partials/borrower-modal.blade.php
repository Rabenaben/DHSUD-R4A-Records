<!-- Borrower Modal -->
<x-modal name="borrower" maxWidth="4xl">
    <div class="relative p-6">
        <button
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
            onclick="closeModal('borrower')">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <h2 class="mb-4 text-xl font-semibold" id="modal-title">Add New Borrower Record</h2>
        <form id="borrower-form">
            @csrf
            <!-- ID and Docket No. -->
            <div class="mb-4 flex gap-4">
                <div class="flex-1">
                    <x-input-label value="ID" />
                    <x-modal-input id="borrower-id" name="id" placeholder="ID" readonly />
                </div>
                <div class="flex-1">
                    <x-input-label value="Docket No." />
                    <x-modal-input id="docket-no" name="docket_number" placeholder="Docket No." required />
                </div>
            </div>

            <!-- File Name and File Location -->
            <div class="mb-4 flex gap-4">
                <div class="flex-1">
                    <x-input-label value="File Name" />
                    <x-modal-input id="file-name" name="file_name" placeholder="File Name" required />
                </div>
                <div class="flex-1">
                    <x-input-label value="File Location" />
                    <select id="file-location" name="file_location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select File Location</option>
                        <option value="REM Records">REM Records</option>
                        <option value="HOA Records">HOA Records</option>
                    </select>
                </div>
            </div>

            <!-- Borrower's Name -->
            <div class="mb-4">
                <x-input-label value="Borrower's Name" />
                <x-modal-input id="borrower-name" name="borrower_name" placeholder="Borrower's Name" required />
            </div>

            <!-- Date & Time -->
            <div class="mb-4">
                <x-input-label value="Date & Time" />
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Loaned</label>
                        <input type="datetime-local" id="date-loaned" name="date_borrowed" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Returned</label>
                        <input type="datetime-local" id="date-returned" name="date_returned" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Optional" />
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <x-input-label value="Status" />
                <select id="status" name="status_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">Select Status</option>
                    @foreach($recordStatuses as $status)
                        <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Remarks -->
            <div class="mb-4">
                <x-input-label value="Remarks" />
                <textarea id="remarks" name="remarks" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" placeholder="Remarks"></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-4">
                <button type="button" id="cancel-btn" class="rounded-lg bg-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-400">Cancel</button>
                <button type="submit" id="save-btn" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</x-modal>
