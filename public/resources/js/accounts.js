document.addEventListener('DOMContentLoaded', () => {
    const statusFilter = document.getElementById('status-filter');
    const searchInput = document.getElementById('search-name');
    const table = document.getElementById('accounts-table');
    const addUserBtn = document.getElementById('add-user-btn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const sendAjaxRequest = async (url, method = 'POST', body = null) => {
        try {
            const response = await fetch(url, { method, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body });
            const result = await response.json();
        window.showToast(result.success ? result.message : 'Error: ' + (result.message || 'Unknown error'), result.success ? 'success' : 'error');
            return result.success ? result : null;
    } catch { window.showToast('Network error', 'error'); return null; }
    };

    const updateRow = (row, user) => {
        const cells = row.cells;
        cells[1].textContent = user.name;
        cells[2].textContent = user.username;
        cells[3].textContent = user.role;
        cells[4].textContent = user.remarks || '';
        cells[5].innerHTML = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white ${user.status === 'active' ? 'bg-green-500' : 'bg-red-500'}">${user.status.charAt(0).toUpperCase() + user.status.slice(1)}</span>`;
        row.setAttribute('data-user', JSON.stringify(user));
    };

    const addNewRow = user => {
        const tbody = table.querySelector('tbody');
        const accountNo = String(tbody.rows.length + 1).padStart(3, '0');
        const newRow = document.createElement('tr');
        newRow.setAttribute('data-id', user.id);
        newRow.setAttribute('data-user', JSON.stringify(user));
        newRow.innerHTML = createUserRowHTML(user, accountNo);
        tbody.appendChild(newRow);
    };

    const validateForm = (form, isAdd) => {
        const name = form.querySelector('[name="name"]').value.trim();
        const username = form.querySelector('[name="username"]').value.trim();
        if (!/^[A-Za-z\s]+$/.test(name)) return window.showToast('Name can only contain letters', 'error'), false;
        if (!/^[A-Za-z0-9]+$/.test(username)) return window.showToast('Username can only contain letters and numbers', 'error'), false;
        const existing = Array.from(table.querySelectorAll('tbody tr td:nth-child(3)')).map(td => td.textContent.toLowerCase());
        if (isAdd && existing.includes(username.toLowerCase())) return window.showToast('Username already exists', 'error'), false;
        if (isAdd) {
            const password = form.querySelector('[name="password"]').value;
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;
            if (!regex.test(password)) return window.showToast('Password must be at least 8 characters and include uppercase, lowercase, number, and special character', 'error'), false;
        }
        return true;
    };

    const createUserRowHTML = (user, accountNo) => {
        const status = user.status.charAt(0).toUpperCase() + user.status.slice(1);
        const statusBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white ${user.status === 'active' ? 'bg-green-500' : 'bg-red-500'}">${status}</span>`;
        return `
            <td class="px-6 py-4 text-center text-sm font-medium text-gray-900">${accountNo}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">${user.name}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">${user.username}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">${user.role}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">${user.remarks || ''}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">${statusBadge}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">
                <button class="edit-btn text-blue-600 hover:text-blue-900" data-id="${user.id}">Edit</button>
            </td>
        `;
    };

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

    const addForm = document.getElementById('add-user-form');
    if (addForm) addForm.addEventListener('submit', async e => {
        e.preventDefault();
        if (!validateForm(addForm, true)) return;
        const result = await sendAjaxRequest(addForm.action, 'POST', new FormData(addForm));
        if (result) {
            addNewRow(result.user);
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-user-modal' } }));
        }
    });

    const editForm = document.getElementById('edit-user-form');
    if (editForm) editForm.addEventListener('submit', async e => {
        e.preventDefault();
        if (!validateForm(editForm, false)) return;
        const result = await sendAjaxRequest(editForm.action, 'POST', new FormData(editForm));
        if (result) {
            const row = document.querySelector(`tr[data-id="${result.user.id}"]`);
            if (row) updateRow(row, result.user);
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'edit-user-modal' } }));
        }
    });

    if (table) table.addEventListener('click', async e => {
        const editBtn = e.target.closest('.edit-btn');
        if (editBtn) {
            const user = JSON.parse(editBtn.closest('tr').dataset.user);
            document.getElementById('edit-name').value = user.name;
            document.getElementById('edit-username').value = user.username;
            document.getElementById('edit-role').value = user.role;
            document.getElementById('edit-remarks').value = user.remarks ?? '';
            document.getElementById('edit-user-form').action = `/users/${user.id}`;
            const archiveBtn = document.getElementById('archive-btn');
            archiveBtn.textContent = user.status === 'active' ? 'Archive' : 'Unarchive';
            archiveBtn.dataset.id = user.id;
            archiveBtn.dataset.action = user.status === 'active' ? 'archive' : 'unarchive';
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'edit-user-modal' } }));
        }
    });

    const archiveBtn = document.getElementById('archive-btn');
    if (archiveBtn) archiveBtn.addEventListener('click', () => {
        const id = archiveBtn.dataset.id;
        const action = archiveBtn.dataset.action;
        const confirmMessage = `Are you sure you want to ${action} this user?`;
        document.getElementById('confirm-message').textContent = confirmMessage;
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'confirm-archive-modal' } }));
    });

    const confirmYesBtn = document.getElementById('confirm-yes-btn');
    if (confirmYesBtn) confirmYesBtn.addEventListener('click', async () => {
        const id = archiveBtn.dataset.id;
        const action = archiveBtn.dataset.action;
        const result = await sendAjaxRequest(`/users/${id}/${action}`, 'PATCH');
        if (result) {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) updateRow(row, result.user);
            // Update button text and action
            archiveBtn.textContent = result.user.status === 'active' ? 'Archive' : 'Unarchive';
            archiveBtn.dataset.action = result.user.status === 'active' ? 'archive' : 'unarchive';
            // Close both modals after successful action
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-archive-modal' } }));
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'edit-user-modal' } }));
        }
    });

    if (addUserBtn) addUserBtn.addEventListener('click', () => window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'add-user-modal' } })));

    // Initialize password strength checker for add-user modal
    const initPasswordStrengthChecker = () => {
        const pwInput = document.getElementById('password-input');
        if (!pwInput) return;
        const bar = document.getElementById('password-strength-bar');
        const items = Array.from(document.querySelectorAll('#password-requirements li'));
        const rules = [
            { rule: 'length', regex: /.{8,}/ },
            { rule: 'uppercase', regex: /[A-Z]/ },
            { rule: 'lowercase', regex: /[a-z]/ },
            { rule: 'number', regex: /[0-9]/ },
            { rule: 'special', regex: /[!@#$%^&*(),.?":{}|<>]/ }
        ];
        const getColor = (count) => {
            if (count === 0) return 'bg-gray-400';
            if (count === 1) return 'bg-red-500';
            if (count === 2) return 'bg-yellow-500';
            if (count === 3 || count === 4) return 'bg-blue-500';
            return 'bg-green-600';
        };
        pwInput.addEventListener('input', () => {
            let passed = 0;
            items.forEach(item => {
                const ok = rules.find(r => r.rule === item.dataset.rule).regex.test(pwInput.value);
                if (ok) passed++;
                item.textContent = (ok ? '✅' : '●') + ' ' + item.dataset.text;
                item.className = ok ? 'text-green-600 text-xs' : 'text-gray-500 text-xs';
            });
            bar.style.width = `${(passed / rules.length) * 100}%`;
            bar.className = `h-2 rounded transition-all duration-300 ${getColor(passed)}`;
        });
    };

    initPasswordStrengthChecker();
});
