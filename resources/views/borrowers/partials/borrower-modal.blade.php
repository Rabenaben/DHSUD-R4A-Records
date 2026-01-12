<!-- Borrower Modal -->
<x-modal name="borrower" maxWidth="4xl">
    <div class="relative p-6">
        <h2 class="mb-4 text-xl font-semibold" id="modal-title">Add New Borrower Record</h2>
        <form id="borrower-form" method="POST" action="{{ route('borrowers.store') }}">
            @csrf
            <!-- Borrower's Name and Date -->
            <div class="mb-4 flex gap-4">
                <div class="flex-1">
                    <x-input-label value="Borrower's Name" />
                    <x-modal-input id="borrower-name" name="borrower_name" placeholder="Borrower's Name" required />
                </div>
                <div class="flex-1">
                    <x-input-label value="Date Borrowed" />
                    <input
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100"
                        id="date-loaned" type="date" name="date_borrowed" disabled value="{{ date('Y-m-d') }}" />
                </div>
            </div>
            <input type="hidden" id="existing-borrower-name" value="">

or            <!-- File Location and Docket No. -->
            <div class="mb-4 flex gap-4">
                <div class="flex-1">
                    <x-input-label value="File Location" />
                    <select
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        id="file-location" name="file_location" required>
                        <option value="">Select File Location</option>
                        <option value="REM Records">REM Records</option>
                        <option value="HOA Records">HOA Records</option>
                    </select>
                </div>
                <div class="flex-1">
                    <x-input-label value="Docket No." />
                    <x-modal-input id="docket-no" name="docket_number" placeholder="Docket No." list="docket-list" required />
                    <datalist id="hoa-docket-list">
                        @foreach($hoaDockets as $docket)
                            <option value="{{ $docket }}">
                        @endforeach
                    </datalist>
                    <datalist id="rem-docket-list">
                        @foreach($remDockets as $docket)
                            <option value="{{ $docket }}">
                        @endforeach
                    </datalist>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-4">
                <button class="rounded-lg bg-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-400"
                    id="cancel-btn" type="button">Cancel</button>
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    id="save-btn" type="submit">Save</button>
            </div>
        </form>
    </div>
</x-modal>
