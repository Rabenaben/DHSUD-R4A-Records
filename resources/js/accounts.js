// public/js/accounts.js
document.addEventListener('DOMContentLoaded', function () {
    const statusFilter = document.getElementById('status-filter');
    const searchInput = document.getElementById('search-name');
    const table = document.getElementById('accounts-table');
    const addUserBtn = document.getElementById('add-user-btn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Helper functions
    async function sendAjaxRequest(url, method = 'POST', body = null) {
        try {
            const response = await fetch(url, {
                method,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body
            });
            const result = await response.json();
            if (result.success) {
                showToast(result.message, 'success');
                return result;
            } else {
                showToast('Error: ' + (result.message || 'Unknown error'), 'error');
                return null;
            }
        } catch (error) {
            showToast('Network error', 'error');
            return null;
        }
    }

    function updateRow(row, user) {
        const cells = row.cells;
        cells[1].textContent = user.name;
        cells[2].textContent = user.username;
        cells[3].textContent = user.role;
        cells[4].textContent = user.status.charAt(0).toUpperCase() + user.status.slice(1);
        cells[6].textContent = user.remarks || '';
        const actionCell = cells[5];
        actionCell.innerHTML = user.status === 'active'
            ? `<button class="archive-btn text-red-600 hover:text-red-900" data-id="${user.id}" data-action="archive">Archive</button>`
            : `<button class="archive-btn text-green-600 hover:text-green-900" data-id="${user.id}" data-action="unarchive">Unarchive</button>`;
        row.setAttribute('data-user', JSON.stringify(user));
    }

    function addNewRow(user) {
        const tbody = table.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr');
        const newIndex = rows.length;
        const accountNo = String(newIndex + 1).padStart(3, '0');
        const status = user.status.charAt(0).toUpperCase() + user.status.slice(1);
        const actionBtn = user.status === 'active'
            ? `<button class="archive-btn text-red-600 hover:text-red-900" data-id="${user.id}" data-action="archive">Archive</button>`
            : `<button class="archive-btn text-green-600 hover:text-green-900" data-id="${user.id}" data-action="unarchive">Unarchive</button>`;
        const newRow = document.createElement('tr');
        newRow.setAttribute('data-id', user.id);
        newRow.setAttribute('data-user', JSON.stringify(user));
        newRow.innerHTML = `
            <td class="px-6 py-4 text-center text-sm font-medium text-gray-900">${accountNo}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">${user.name}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">${user.username}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">${user.role}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">${status}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">${actionBtn}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">${user.remarks || ''}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">
                <button class="edit-btn text-blue-600 hover:text-blue-900" data-id="${user.id}">Edit</button>
            </td>
        `;
        tbody.appendChild(newRow);
    }

    // Filtering
    if (statusFilter && searchInput && table) {
        const filterTable = () => {
            const statusValue = statusFilter.value;
            const searchValue = searchInput.value.toLowerCase();
            table.querySelectorAll('tbody tr').forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const status = row.cells[4].textContent.toLowerCase();
                row.style.display = (statusValue === 'all' || status === statusValue) && name.includes(searchValue) ? '' : 'none';
            });
        };
        statusFilter.addEventListener('change', filterTable);
        searchInput.addEventListener('input', filterTable);
    }

    // Add User button
    if (addUserBtn) {
        addUserBtn.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'add-user-modal' } }));
        });
    }

    // Table actions (edit and archive)
    if (table) {
        table.addEventListener('click', async (e) => {
            const editBtn = e.target.closest('.edit-btn');
            const archiveBtn = e.target.closest('.archive-btn');
            if (editBtn) {
                const row = editBtn.closest('tr');
                const user = JSON.parse(row.dataset.user);
                document.getElementById('edit-name').value = user.name;
                document.getElementById('edit-username').value = user.username;
                document.getElementById('edit-role').value = user.role;
                document.getElementById('edit-remarks').value = user.remarks ?? '';
                document.getElementById('edit-user-form').action = `/users/${user.id}`;
                window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'edit-user-modal' } }));
            } else if (archiveBtn) {
                const id = archiveBtn.dataset.id;
                const action = archiveBtn.dataset.action;
                const row = archiveBtn.closest('tr');
                const result = await sendAjaxRequest(`/users/${id}/${action}`, 'PATCH');
                if (result) updateRow(row, result.user);
            }
        });
    }

    // Add user form
    const addForm = document.getElementById('add-user-form');
    if (addForm) {
        addForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const result = await sendAjaxRequest(addForm.action, 'POST', new FormData(addForm));
            if (result) {
                addNewRow(result.user);
                window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-user-modal' } }));
            }
        });
    }

    // Edit user form
    const editForm = document.getElementById('edit-user-form');
    if (editForm) {
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const result = await sendAjaxRequest(editForm.action, 'POST', new FormData(editForm));
            if (result) {
                const row = document.querySelector(`tr[data-id="${result.user.id}"]`);
                if (row) updateRow(row, result.user);
                window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'edit-user-modal' } }));
            }
        });
    }
});
