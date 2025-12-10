<section>
    <!-- Filters, Search, and Add User -->
    <div class="mt-2 flex items-center justify-between bg-white p-4 shadow-sm sm:rounded-lg">
        <div class="flex items-center space-x-4">
            <input class="rounded-md border border-gray-300 px-3 py-2 text-sm" id="search-name" type="text"
                placeholder="Enter name...">
            <select class="rounded-md border border-gray-300 px-6 py-2 text-sm" id="status-filter">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="flex items-center space-x-4">

            <button class="ml-4 rounded bg-blue-500 px-4 py-2 text-white" id="add-user-btn">Add User</button>
        </div>
    </div>

    <!-- Table -->
    <div class="mt-4 overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="border-b border-gray-200 bg-white p-6">
            <table class="min-w-full divide-y divide-blue-400" id="accounts-table">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach (['Account No.', 'Name', 'Username', 'Role', 'Status', 'Action', 'Division', 'Edit User'] as $header)
                            <th class="text-black-500 px-6 py-3 text-center text-xs font-bold uppercase tracking-wider">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach ($users as $index => $user)
                        <x-accounts.user-row :user="$user" :index="$index" />
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
