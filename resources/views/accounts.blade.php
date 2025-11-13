<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Accounts') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Section Header Card -->
        <div class="relative bg-transparent py-4">
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
        <div class="mt-6 flex items-center justify-between bg-white p-4 shadow-sm sm:rounded-lg">
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700" for="status-filter">Filter by Status:</label>
                <select class="rounded-md border border-gray-300 px-3 py-2 text-sm" id="status-filter">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700" for="search-name">Search by Name:</label>
                <input class="rounded-md border border-gray-300 px-3 py-2 text-sm" id="search-name" type="text"
                    placeholder="Enter name...">
                <button class="ml-4 rounded bg-blue-500 px-4 py-2 text-white" id="add-user-btn">Add User</button>
            </div>
        </div>

        <!-- Table -->
        <div class="mt-4 overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="border-b border-gray-200 bg-white p-6">
                <table class="min-w-full divide-y divide-gray-200" id="accounts-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                scope="col">Account No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                scope="col">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                scope="col">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                scope="col">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                scope="col">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                scope="col">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                scope="col">Remarks</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($users as $index => $user)
                            <tr data-id="{{ $user->id }}">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $user->name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $user->username }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $user->role }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ ucfirst($user->status) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    @if ($user->status == 'active')
                                        <form action="{{ route('users.archive', $user->id) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button class="text-red-600 hover:text-red-900"
                                                type="submit">Archive</button>
                                        </form>
                                    @else
                                        <form action="{{ route('users.unarchive', $user->id) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button class="text-green-600 hover:text-green-900"
                                                type="submit">Unarchive</button>
                                        </form>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $user->remarks }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    <button class="edit-btn text-blue-600 hover:text-blue-900"
                                        data-id="{{ $user->id }}">Edit</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Add User Modal -->
    <x-modal name="add-user-modal" maxWidth="md" :show="$errors->any()">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Add New User</h2>

            <form class="mt-6" action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="name">Name</label>
                        <input
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            id="name" type="text" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="username">Username</label>
                        <input
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            id="username" type="text" name="username" value="{{ old('username') }}" required>
                        @error('username')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="password">Password</label>
                        <input
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            id="password" type="password" name="password" required>
                        @error('password')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="role">Role</label>
                        <input
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            id="role" type="text" name="role" value="{{ old('role') }}" required>
                        @error('role')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700" for="remarks">Remarks</label>
                        <textarea
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            id="remarks" name="remarks" rows="3">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button class="mr-3 rounded-md bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400"
                        type="button"
                        x-on:click="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-user-modal' } }))">Cancel</button>
                    <button class="rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-600" type="submit">Add
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="edit-name">Name</label>
                        <input
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            id="edit-name" type="text" name="name" required>
                        @error('name')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="edit-username">Username</label>
                        <input
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            id="edit-username" type="text" name="username" required>
                        @error('username')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="edit-role">Role</label>
                        <input
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            id="edit-role" type="text" name="role" required>
                        @error('role')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700" for="edit-remarks">Remarks</label>
                        <textarea
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            id="edit-remarks" name="remarks" rows="3"></textarea>
                        @error('remarks')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button class="mr-3 rounded-md bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400"
                        type="button"
                        x-on:click="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'edit-user-modal' } }))">Cancel</button>
                    <button class="rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-600"
                        type="submit">Update User</button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('status-filter');
            const searchInput = document.getElementById('search-name');
            const table = document.getElementById('accounts-table');
            const rows = table.querySelectorAll('tbody tr');

            function filterTable() {
                const statusValue = statusFilter.value;
                const searchValue = searchInput.value.toLowerCase();

                rows.forEach(row => {
                    const name = row.cells[1].textContent.toLowerCase();
                    const status = row.cells[4].textContent.toLowerCase();

                    const matchesStatus = statusValue === 'all' || status === statusValue;
                    const matchesSearch = name.includes(searchValue);

                    if (matchesStatus && matchesSearch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            statusFilter.addEventListener('change', filterTable);
            searchInput.addEventListener('input', filterTable);

            // Add User button
            document.getElementById('add-user-btn').addEventListener('click', function() {
                window.dispatchEvent(new CustomEvent('open-modal', {
                    detail: {
                        name: 'add-user-modal'
                    }
                }));
            });

            // Edit User functionality
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id');
                    const row = this.closest('tr');
                    const name = row.cells[1].textContent;
                    const username = row.cells[2].textContent;
                    const role = row.cells[3].textContent;
                    const remarks = row.cells[6].textContent;

                    document.getElementById('edit-name').value = name;
                    document.getElementById('edit-username').value = username;
                    document.getElementById('edit-role').value = role;
                    document.getElementById('edit-remarks').value = remarks;

                    const form = document.getElementById('edit-user-form');
                    form.action = `/users/${userId}`;

                    window.dispatchEvent(new CustomEvent('open-modal', {
                        detail: {
                            name: 'edit-user-modal'
                        }
                    }));
                });
            });
        });
    </script>
</x-app-layout>
