// Archive search functionality
function initArchiveSearch() {
    const searchInput = document.getElementById('archiveSearchInput');
    const tableBody = document.querySelector('#archiveTable tbody');
    const noRecordsRow = document.getElementById('no-archived-records-row');

    if (!searchInput || !tableBody) return;

    const filterRows = () => {
        const searchValue = (searchInput.value || '').trim().toLowerCase();
        let visibleRows = 0;

        tableBody.querySelectorAll('.archive-row').forEach(row => {
            const type = row.dataset.type.toLowerCase();
            const docket = row.dataset.docket.toLowerCase();
            const name = row.dataset.name.toLowerCase();

            const matches = [type, docket, name].some(text => text.includes(searchValue));

            row.style.display = matches ? '' : 'none';
            if (matches) visibleRows++;
        });

        // Toggle the no records row
        if (noRecordsRow) {
            noRecordsRow.style.display = visibleRows > 0 ? 'none' : '';
        }
    };

    searchInput.addEventListener('input', filterRows);

    // Initial filter
    filterRows();
}

// Unarchive functionality
function initUnarchiveButtons() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('unarchive-btn')) {
            const button = e.target;
            const type = button.dataset.type;
            const id = button.dataset.id;

            if (confirm('Are you sure you want to unarchive this record?')) {
                unarchiveRecord(type, id, button);
            }
        }
    });
}

function unarchiveRecord(type, id, button) {
    button.disabled = true;
    button.textContent = 'Unarchiving...';

    fetch(`/${type}/${id}/unarchive`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the row from the table
            button.closest('tr').remove();
            showToast('Record unarchived successfully!', 'success');

            // Check if table is empty and show no records message
            const tableBody = document.querySelector('#archiveTable tbody');
            const archiveRows = tableBody.querySelectorAll('.archive-row');
            const noRecordsRow = document.getElementById('no-archived-records-row');

            if (archiveRows.length === 0 && noRecordsRow) {
                noRecordsRow.style.display = '';
            }
        } else {
            showToast('Failed to unarchive record.', 'error');
            button.disabled = false;
            button.textContent = 'Unarchive';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while unarchiving the record.', 'error');
        button.disabled = false;
        button.textContent = 'Unarchive';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initArchiveSearch();
    initUnarchiveButtons();
});
