<!-- Borrower Modal -->
<x-modal name="borrower" maxWidth="4xl">
    <div class="relative p-6">
        <h2 class="mb-4 text-xl font-semibold" id="modal-title">Add New Borrower Record</h2>
        <form id="borrower-form" method="POST" action="{{ route('borrowers.store') }}">
            @csrf
            <!-- Borrower's Name and Division -->
            <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="flex-1">
                    <x-input-label value="Borrower's Name" />
                    <x-modal-input id="borrower-name" name="borrower_name" placeholder="Borrower's Name" required />
                </div>
                <div class="flex-1">
                    <x-input-label value="Division" />
                    <select
                        class="w-full rounded-lg border border-gray-300 px-2 py-2 outline-none focus:border-blue-600"
                        id="division" name="division" required>
                        <option value="">Select Division</option>
                        @foreach ($divisions as $division)
                            <option value="{{ $division }}">{{ $division }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <input id="existing-borrower-name" type="hidden" value="">

            <!-- Project Information -->
            <div class="mb-4">
                <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold text-gray-700">Project Information
                </h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label value="File Location" />
                        <select
                            class="w-full rounded-lg border border-gray-300 px-2 py-2 outline-none focus:border-blue-600"
                            id="file-location" name="file_location" required>
                            <option value="">Select File Location</option>
                            <option value="REM Records">REM Records</option>
                            <option value="HOA Records">HOA Records</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Docket No." />
                        <x-modal-input id="borrower-docket-no" name="docket_number" placeholder="Docket No."
                            list="borrower-docket-list" required />
                        <datalist id="hoa-docket-list">
                            @foreach ($hoaDockets as $docket)
                                <option value="{{ $docket }}">
                            @endforeach
                        </datalist>
                        <datalist id="rem-docket-list">
                            @foreach ($remDockets as $docket)
                                <option value="{{ $docket }}">
                            @endforeach
                        </datalist>
                    </div>
                    <p class="mt-1 text-xs italic text-gray-500">Name and location will be auto-filled when docket
                        number is selected</p>
                </div>
            </div>

            <!-- Project Details and Date Borrowed -->
            <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <x-input-label value="Name of Project / HOA" />
                    <x-modal-input id="borrower-project-name" name="project_name" placeholder="Project / HOA Name"
                        required />
                </div>
                <div>
                    <x-input-label value="Location" />
                    <x-modal-input id="borrower-location" name="location" placeholder="Location" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label value="Date Borrowed" />
                    <input class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" id="date-loaned"
                        type="date" name="date_borrowed" disabled value="{{ date('Y-m-d') }}" />
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
