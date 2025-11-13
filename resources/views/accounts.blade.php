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

        @if(session('success'))
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filters, Search, and Add User -->
        <div class="mt-6 flex justify-between items-center bg-white p-4 shadow-sm sm:rounded-lg">
            <div class="flex items-center space-x-4">
                <label for="status-filter" class="text-sm font-medium text-gray-700">Filter by Status:</label>
                <select id="status-filter" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="flex items-center space-x-4">
                <label for="search-name" class="text-sm font-medium text-gray-700">Search by Name:</label>
                <input type="text" id="search-name" placeholder="Enter name..." class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <button id="add-user-btn" class="bg-blue-500 text-white px-4 py-2 rounded ml-4">Add User</button>
            </div>
        </div>

        <!-- Table -->
        <div class="mt-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <table id="accounts-table" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account No.</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $index => $user)
                        <tr data-id="{{ $user->id }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->username }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->role }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($user->status) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($user->status == 'active')
                                    <form action="{{ route('users.archive', $user->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Archive</button>
                                    </form>
                                @else
                                    <form action="{{ route('users.unarchive', $user->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-600 hover:text-green-900">Unarchive</button>
                                    </form>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->remarks }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <button class="text-blue-600 hover:text-blue-900 edit-btn" data-id="{{ $user->id }}">Edit</button>
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

            <form action="{{ route('users.store') }}" method="POST" class="mt-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        @error('username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <input type="text" name="role" id="role" value="{{ old('role') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('remarks') }}</textarea>
                        @error('remarks') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" x-on:click="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-user-modal' } }))" class="mr-3 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Add User</button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Edit User Modal -->
    <x-modal name="edit-user-modal" maxWidth="md">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Edit User</h2>

            <form id="edit-user-form" method="POST" class="mt-6">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="edit-name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="edit-name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="edit-username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" id="edit-username" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        @error('username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="edit-role" class="block text-sm font-medium text-gray-700">Role</label>
                        <input type="text" name="role" id="edit-role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="edit-remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                        <textarea name="remarks" id="edit-remarks" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                        @error('remarks') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" x-on:click="window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'edit-user-modal' } }))" class="mr-3 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Update User</button>
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
                window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'add-user-modal' } }));
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

                    window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'edit-user-modal' } }));
                });
            });
        });
    </script>
</x-app-layout>
