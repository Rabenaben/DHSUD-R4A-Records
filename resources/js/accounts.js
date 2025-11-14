// public/js/accounts.js
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('status-filter');
    const searchInput = document.getElementById('search-name');
    const table = document.getElementById('accounts-table');
    const addUserBtn = document.getElementById('add-user-btn');

    // ---------------------------------------------------------
    // Only run filtering if ALL required elements exist
    // ---------------------------------------------------------
    if (statusFilter && searchInput && table) {

        function filterTable() {
            const statusValue = statusFilter.value;
            const searchValue = searchInput.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const status = row.cells[4].textContent.toLowerCase();

                const matchesStatus = statusValue === 'all' || status === statusValue;
                const matchesSearch = name.includes(searchValue);

                row.style.display = (matchesStatus && matchesSearch) ? '' : 'none';
            });
        }

        statusFilter.addEventListener('change', filterTable);
        searchInput.addEventListener('input', filterTable);

        // Edit buttons
        table.addEventListener('click', function(e) {
            const btn = e.target.closest('.edit-btn');
            if (!btn) return;

            const row = btn.closest('tr');
            const user = JSON.parse(row.dataset.user);

            document.getElementById('edit-name').value = user.name;
            document.getElementById('edit-username').value = user.username;
            document.getElementById('edit-role').value = user.role;
            document.getElementById('edit-remarks').value = user.remarks ?? '';

            const form = document.getElementById('edit-user-form');
            form.action = `/users/${user.id}`;

            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: { name: 'edit-user-modal' }
            }));
        });
    }

    // ---------------------------------------------------------
    // Add User button (safe check)
    // ---------------------------------------------------------
    if (addUserBtn) {
        addUserBtn.addEventListener('click', function() {
            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: { name: 'add-user-modal' }
            }));
        });
    }
});
