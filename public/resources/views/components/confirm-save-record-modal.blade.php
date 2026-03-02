<!-- Confirm Save Record Modal -->
@props(['type' => 'HOA'])
<x-modal name="confirm-save-record-modal" maxWidth="sm">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">Confirm Save</h2>
        <p class="mt-4 text-sm text-gray-600">Are you sure you want to save this {{ $type }} record?</p>
        <div class="mt-6 flex justify-end">
            <button class="mr-3 rounded-md bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400" type="button"
                x-on:click="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-save-record-modal' } }))">
                Cancel
            </button>
            <button class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" id="confirm-save-record-yes-btn"
                type="button">
                Yes, Save
            </button>
        </div>
    </div>
</x-modal>
