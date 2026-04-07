<!-- Confirm Archive File Modal -->
<x-modal name="confirm-archive-file-modal" maxWidth="sm">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">Confirm Archive</h2>
        <p class="mt-4 text-sm text-gray-600" id="confirm-archive-file-message">Are you sure you want to archive this file?</p>
        <div class="mt-6 flex justify-end">
            <button class="mr-3 rounded-md bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400" type="button"
                x-on:click="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-archive-file-modal' } }))">
                No
            </button>
            <button class="rounded-md bg-red-500 px-4 py-2 text-white hover:bg-red-900" id="confirm-archive-file-yes-btn"
                type="button">
                Yes
            </button>
        </div>
    </div>
</x-modal>
