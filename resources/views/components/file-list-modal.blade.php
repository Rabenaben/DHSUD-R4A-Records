<!-- File List Modal -->
<x-modal name="file-list" maxWidth="4xl">
    <div class="mb-4 flex items-center justify-between p-6">
        <h3 class="text-lg font-semibold text-gray-900" id="file-list-title">Files</h3>
        <div class="flex items-center space-x-2">
            <x-secondary-button id="add-file-btn">Add File</x-secondary-button>
            <button class="text-gray-400 hover:text-gray-600"
                @click="$dispatch('close-modal', { name: 'file-list' })">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    </div>
    <div class="overflow-x-auto overflow-y-auto max-h-96 flex justify-center">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 w-1/3">
                        File Name</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 w-1/3">
                        Date Modified</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 w-1/3">
                        Last Updated By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white" id="file-list-body">
                {{-- Files will be rendered here via JS --}}
            </tbody>
        </table>
    </div>
</x-modal>
