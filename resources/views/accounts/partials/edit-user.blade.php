<section>
    <!-- Edit User Modal -->
    <x-modal name="edit-user-modal" maxWidth="md">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Edit User</h2>

            <form class="mt-6" id="edit-user-form" method="POST">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <x-accounts.input-field id="edit-name" label="Name" name="name" required />
                    <x-accounts.input-field id="edit-username" label="Username" name="username" required />
                    <x-accounts.select-field id="edit-role" label="Role" name="role" :options="['Admin', 'Staff']" required />
                    <x-accounts.select-field class="md:col-span-2" id="edit-remarks" label="Division" name="remarks"
                        :options="['HREDRD - PRLS', 'HREDRD - EMES', 'RECORDS', 'HOACDD', 'ELUUPDD', 'PHSD']" required />
                </div>

                <div class="mt-6 flex justify-end">
                    <button class="mr-3 rounded-md bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400" type="button"
                        x-on:click="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'edit-user-modal' } }))">
                        Cancel
                    </button>
                    <button class="rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-900" type="submit">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</section>
