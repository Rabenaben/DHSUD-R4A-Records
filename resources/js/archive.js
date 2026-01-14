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

// Archive modal functionality
function initArchiveModal() {
    document.addEventListener('click', function(e) {
        const row = e.target.closest('.archive-row');
        if (!row) return;

        // Prevent opening modal if unarchive button was clicked
        if (e.target.classList.contains('unarchive-file-btn')) return;

        const type = row.dataset.type;
        const docketNo = row.dataset.docket;
        const recordName = row.dataset.name;
        const fileName = row.dataset.file;
        const dateAdded = row.querySelector('td:nth-child(5)').textContent.trim();
        const lastUpdatedBy = row.querySelector('td:nth-child(6)').textContent.trim();
        const fileIndex = row.querySelector('.unarchive-file-btn').dataset.fileIndex;

        // Populate modal fields
        document.getElementById('archive-type').value = type;
        document.getElementById('archive-docket-no').value = docketNo;
        document.getElementById('archive-record-name').value = recordName;
        document.getElementById('archive-file-name').value = fileName;
        document.getElementById('archive-date-added').value = dateAdded;
        document.getElementById('archive-last-updated-by').value = lastUpdatedBy;
        document.getElementById('archive-file-label-preview').value = fileName;

        // Store current archive data for export
        window.currentArchiveType = type;
        window.currentArchiveDocketNo = docketNo;
        window.currentArchiveFileIndex = fileIndex;

        // Load file preview
        loadArchiveFilePreview(type, docketNo, fileIndex);

        // Open modal
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'archive' } }));
    });
}

function loadArchiveFilePreview(type, docketNo, fileIndex) {
    const filePreview = document.getElementById('archive-file-preview');
    const filePlaceholder = document.getElementById('archive-file-placeholder');
    const lowerType = type.toLowerCase();

    // Show loading state
    filePreview.style.display = 'none';
    filePlaceholder.innerHTML = `
        <div class="flex flex-col items-center justify-center h-full">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mb-2"></div>
            <span class="text-gray-500">Loading preview...</span>
        </div>
    `;
    filePlaceholder.style.display = 'flex';

    // Set the preview src directly (assuming archived files can be previewed like active files)
    filePreview.src = `/${lowerType}/${docketNo}/preview/${fileIndex}`;
    filePreview.style.display = 'block';
    filePlaceholder.style.display = 'none';

    // Handle load error
    filePreview.onerror = function() {
        filePreview.style.display = 'none';
        filePlaceholder.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full">
                <svg class="w-12 h-12 text-red-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-gray-500">Error loading preview</span>
            </div>
        `;
        filePlaceholder.style.display = 'flex';
    };
}

function exportArchiveFile() {
    if (window.currentArchiveType && window.currentArchiveDocketNo && window.currentArchiveFileIndex !== undefined) {
        const url = `/records/${window.currentArchiveType}/${window.currentArchiveDocketNo}/download/${window.currentArchiveFileIndex}`;
        window.open(url, '_blank');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initArchiveSearch();
    initUnarchiveButtons();
    initArchiveModal();
});
