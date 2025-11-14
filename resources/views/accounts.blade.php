<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Accounts') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4">
        <!-- Section Header Card -->
        <div class="relative bg-transparent">
            <h2 class="text-2xl font-bold tracking-wide text-black">{{ __('List of Accounts') }}</h2>
            <div class="mt-2 border-b-2 border-gray-700"></div>
        </div>

        @if (session('success'))
            <div class="relative mt-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filters, Search, and Add User -->
        @include('partials.accounts-table')
    </div>
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
                    <x-select-field class="md:col-span-2" label="Division" name="remarks" :options="['HREDRD', 'RECORD SECTION', 'HOACD', 'PRLS', 'ELUPD', 'ORD']" />
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

    <!-- Edit User Modal -->
    <x-modal name="edit-user-modal" maxWidth="md">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Edit User</h2>

            <form class="mt-6" id="edit-user-form" method="POST">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <x-input-field id="edit-name" label="Name" name="name" required />
                    <x-input-field id="edit-username" label="Username" name="username" required />
                    <x-select-field id="edit-role" label="Role" name="role" :options="['Admin', 'Staff']" required />
                    <x-select-field class="md:col-span-2" id="edit-remarks" label="Division" name="remarks" :options="['HREDRD', 'RECORD SECTION', 'HOACD', 'PRLS', 'ELUPD', 'ORD']" />
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

    <script src="{{ asset('js/accounts.js') }}"></script>
</x-app-layout>
