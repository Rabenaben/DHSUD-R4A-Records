<section>
    <!-- Add User Modal -->
    <x-modal name="add-user-modal" maxWidth="md" :show="$errors->any()">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Add New User</h2>
            <form class="mt-6" action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <x-input-field label="Name" name="name" />
                    <x-input-field label="Username" name="username" />
                    <x-input-field label="Password" name="password" type="password" />
                    <x-select-field label="Role" name="role" :options="['Admin', 'Staff']" />
                    <x-select-field class="md:col-span-2" label="Division" name="remarks" :options="['HREDRD - PRLS', 'HREDRD - EMES', 'RECORDS', 'HOACDD', 'ELUUPDD', 'PHSD']" />
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button class="mr-3 rounded-md bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400" type="button"
                        x-on:click="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-user-modal' } }))">
                        Cancel
                    </button>
                    <button class="rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-900" type="submit">Add
                        User</button>
                </div>
            </form>
        </div>
    </x-modal>
</section>
