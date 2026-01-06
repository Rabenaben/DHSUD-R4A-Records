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
        if (e.target.classList.contains('unarchive-file-btn')) {
            const button = e.target;
            const type = button.dataset.type;
            const docketNo = button.dataset.docket;
            const fileIndex = button.dataset.fileIndex;

            // Set the confirm message
            const confirmMessageEl = document.getElementById('confirm-archive-file-message');
            if (confirmMessageEl) {
                confirmMessageEl.textContent = `Are you sure you want to unarchive this file?`;
            }

            // Store the type, docketNo, and fileIndex for later use
            window.pendingUnarchiveType = type;
            window.pendingUnarchiveDocketNo = docketNo;
            window.pendingUnarchiveFileIndex = fileIndex;
            window.pendingUnarchiveButton = button;

            // Open the confirmation modal
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'confirm-archive-file-modal' } }));

            // Attach event listener to the yes button if not already attached
            const confirmYesBtn = document.getElementById('confirm-archive-file-yes-btn');
            if (confirmYesBtn && !confirmYesBtn.dataset.unarchiveFileListenerAttached) {
                confirmYesBtn.dataset.unarchiveFileListenerAttached = 'true';
                confirmYesBtn.addEventListener('click', () => {
                    const type = window.pendingUnarchiveType;
                    const docketNo = window.pendingUnarchiveDocketNo;
                    const fileIndex = window.pendingUnarchiveFileIndex;
                    const button = window.pendingUnarchiveButton;

                    // Close the modal
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-archive-file-modal' } }));

                    // Proceed with unarchiving
                    unarchiveFile(type, docketNo, fileIndex, button);
                });
            }
        }
    });
}

function unarchiveFile(type, docketNo, fileIndex, button) {
    button.disabled = true;
    button.textContent = 'Unarchiving...';

    fetch(`/records/${type}/${docketNo}/files/${fileIndex}/unarchive`, {
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
            window.showToast('File unarchived successfully!', 'success');

            // Check if table is empty and show no records message
            const tableBody = document.querySelector('#archiveTable tbody');
            const archiveRows = tableBody.querySelectorAll('.archive-row');
            const noRecordsRow = document.getElementById('no-archived-records-row');

            if (archiveRows.length === 0 && noRecordsRow) {
                noRecordsRow.style.display = '';
            }
        } else {
            window.showToast('Failed to unarchive file.', 'error');
            button.disabled = false;
            button.textContent = 'Unarchive';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.showToast('An error occurred while unarchiving the file.', 'error');
        button.disabled = false;
        button.textContent = 'Unarchive';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initArchiveSearch();
    initUnarchiveButtons();
});
