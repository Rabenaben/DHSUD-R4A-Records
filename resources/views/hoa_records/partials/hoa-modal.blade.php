<!-- HOA Modal -->
<x-modal name="hoa" maxWidth="4xl">
    <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-2">

        <!-- Form Section -->
        <div class="flex-1">
            <!-- Basic Information -->
            <h3 class="mb-2 mt-4 text-[15px] font-semibold">Basic Information</h3>
            <div class="mb-2.5 flex gap-2.5">
                <input
                    class="flex-1 rounded-lg border border-gray-300 px-2 py-2 outline-none focus:border-blue-600"
                    id="docket-no" type="text" placeholder="Docket No." readonly>
                <input
                    class="flex-1 rounded-lg border border-gray-300 px-2 py-2 outline-none focus:border-blue-600"
                    id="hoa-name" type="text" placeholder="HOA Name" readonly>
            </div>

            <!-- Location -->
            <h3 class="mb-2 mt-4 text-[15px] font-semibold">Location</h3>
            <div class="mb-2.5 flex gap-2.5">
                <input
                    class="flex-1 rounded-lg border border-gray-300 px-2 py-2 outline-none focus:border-blue-600"
                    id="province" type="text" placeholder="Province" readonly>
                <input
                    class="flex-1 rounded-lg border border-gray-300 px-2 py-2 outline-none focus:border-blue-600"
                    id="municipality" type="text" placeholder="Municipality" readonly>
            </div>

            <!-- Additional Information -->
            <h3 class="mb-2 mt-4 text-[15px] font-semibold">Additional Information</h3>
            <div class="mb-2.5 flex gap-2.5">
                <input
                    class="flex-1 rounded-lg border border-gray-300 px-2 py-2 outline-none focus:border-blue-600"
                    id="status" type="text" placeholder="Status" readonly>
                <input
                    class="flex-1 rounded-lg border border-gray-300 px-2 py-2 outline-none focus:border-blue-600"
                    id="quantity" type="text" placeholder="Quantity" readonly>
            </div>

            <textarea
                class="min-h-[50px] w-full resize-none rounded-lg border border-gray-300 p-2 outline-none focus:border-blue-600"
                id="remarks" placeholder="Remarks" readonly></textarea>

            <button
                class="mx-auto mt-4 block rounded-lg bg-green-600 px-6 py-2 text-sm font-semibold text-white transition hover:bg-green-700">EDIT</button>
        </div>

        <!-- File Section -->
        <div class="flex flex-1 flex-col items-center">
            <div class="h-[340px] w-[90%] rounded-lg border border-gray-300 bg-gray-100 bg-contain bg-center bg-no-repeat"
                id="file-preview"
                style="background-image: url('https://via.placeholder.com/300x400?text=File+Preview')"></div>
            <div class="mb-4 mt-2 text-sm font-medium text-gray-800" id="file-label"></div>
            <div class="flex gap-3">
                <button
                    class="rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white hover:bg-blue-700">IMPORT
                    FILE</button>
                <button
                    class="rounded-lg bg-blue-800 px-6 py-2 font-semibold text-white hover:bg-blue-900">EXPORT
                    FILE</button>
                <button
                    class="rounded-lg bg-red-600 px-6 py-2 font-semibold text-white hover:bg-red-700">ARCHIVE
                    FILE</button>
            </div>
        </div>
    </div>
</x-modal>
