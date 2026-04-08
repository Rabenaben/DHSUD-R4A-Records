// Archive Modal File List Search Functionality
function initArchiveFileListSearch() {
    const searchInput = document.getElementById('archive-files-search');
    const clearBtn = document.getElementById('archive-files-search-clear');
    const tbody = document.getElementById('archive-file-list-body');
    if (!searchInput || !tbody) return;

    function filterFileRows() {
        const searchValue = (searchInput.value || '').trim().toLowerCase();
        let visibleRows = 0;
        tbody.querySelectorAll('tr.archive-file-row').forEach(row => {
            const name = row.children[0]?.textContent?.toLowerCase() || '';
            const date = row.children[1]?.textContent?.toLowerCase() || '';
            const user = row.children[2]?.textContent?.toLowerCase() || '';
            const matches = [name, date, user].some(text => text.includes(searchValue));
            row.style.display = matches ? '' : 'none';
            if (matches) visibleRows++;
        });
        // Show/hide no files row
        let noFilesRow = tbody.querySelector('tr.no-archive-files-row');
        if (!noFilesRow) {
            noFilesRow = document.createElement('tr');
            noFilesRow.className = 'no-archive-files-row';
            const td = document.createElement('td');
            td.colSpan = 3;
            td.className = 'px-6 py-4 text-center text-sm italic text-gray-500';
            td.textContent = 'No files found.';
            noFilesRow.appendChild(td);
            tbody.appendChild(noFilesRow);
        }
        noFilesRow.style.display = visibleRows === 0 ? '' : 'none';
    }

    searchInput.addEventListener('input', function () {
        filterFileRows();
        if (clearBtn) clearBtn.style.display = searchInput.value ? '' : 'none';
    });
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            filterFileRows();
            clearBtn.style.display = 'none';
        });
    }
    // Initial filter
    filterFileRows();
}
// Archive search functionality
function initArchiveSearch() {
    const searchInput = document.getElementById('archiveSearchInput');
    const tableBody = document.querySelector('#archiveTable tbody');
    let noRecordsRow = document.getElementById('no-archived-records-row');

    if (!searchInput || !tableBody) return;

    const filterRows = () => {
        const searchValue = (searchInput.value || '').trim().toLowerCase();
        let visibleRows = 0;
        let currentNoRecordsRow = document.getElementById('no-archived-records-row');

        tableBody.querySelectorAll('.archive-row').forEach(row => {
            const type = row.dataset.type.toLowerCase();
            const docket = row.dataset.docket.toLowerCase();
            const name = row.dataset.name.toLowerCase();

            const matches = [type, docket, name].some(text => text.includes(searchValue));

            row.style.display = matches ? '' : 'none';
            if (matches) visibleRows++;
        });

        // Toggle the no records row
        if (visibleRows === 0) {
            if (!currentNoRecordsRow) {
                currentNoRecordsRow = document.createElement('tr');
                currentNoRecordsRow.id = 'no-archived-records-row';
                currentNoRecordsRow.innerHTML = '<td class="px-6 py-4 text-center text-sm italic text-gray-500" colspan="7">No archived files found</td>';
                tableBody.appendChild(currentNoRecordsRow);
            }
            currentNoRecordsRow.style.display = '';
        } else if (currentNoRecordsRow) {
            currentNoRecordsRow.style.display = 'none';
        }
    };

    searchInput.addEventListener('input', filterRows);

    // Initial filter
    filterRows();
}

// Unarchive functionality
function initUnarchiveButtons() {
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('unarchive-file-btn')) {
            const button = e.target;
            const type = button.dataset.type;
            const docketNo = button.dataset.docket;

            // Set the confirm message
            const confirmMessageEl = document.getElementById('confirm-archive-file-message');
            if (confirmMessageEl) {
                confirmMessageEl.textContent = `Are you sure you want to unarchive docket ${docketNo}?`;
            }

            // Store the type, docketNo for later use
            window.pendingUnarchiveType = type;
            window.pendingUnarchiveDocketNo = docketNo;
            window.pendingUnarchiveButton = button;

            // Open the confirmation modal
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'confirm-archive-file-modal' } }));

            // Attach event listener to the yes button if not already attached
            const confirmYesBtn = document.getElementById('confirm-archive-file-yes-btn');
            if (confirmYesBtn && !confirmYesBtn.dataset.unarchiveDocketListenerAttached) {
                confirmYesBtn.dataset.unarchiveDocketListenerAttached = 'true';
                confirmYesBtn.addEventListener('click', () => {
                    const type = window.pendingUnarchiveType;
                    const docketNo = window.pendingUnarchiveDocketNo;
                    const button = window.pendingUnarchiveButton;

                    // Close the modal
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-archive-file-modal' } }));

                    // Proceed with unarchiving
                    unarchiveDocket(type, docketNo, button);
                });
            }
        }
    });
}



// Archive modal functionality
function initArchiveModal() {
    document.addEventListener('click', function (e) {
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
        // No initial fileIndex for per-docket row

        // Load archived files list (no initial preview)
        window.loadArchiveFileList(type.toLowerCase(), docketNo);

        // Open modal
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'archive' } }));
    });
}

window.archiveLoadFilePreview = function (fileIndex) {
    window.currentArchiveFileIndex = fileIndex;
    document.getElementById('archive-file-label-preview').value = window.pendingArchiveFileName || document.querySelector('#archive-file-list-body tr:nth-child(1) td:first-child').textContent.trim();
    loadArchiveFilePreview(window.currentArchiveType, window.currentArchiveDocketNo, fileIndex);
    // Switch to preview view
    document.getElementById('archive-file-list-view').style.display = 'none';
    document.getElementById('archive-file-preview-view').style.display = 'flex';
};

window.unarchiveArchiveFile = function () {
    if (
        !window.currentArchiveType ||
        !window.currentArchiveDocketNo ||
        window.currentArchiveFileIndex === undefined
    ) {
        return;
    }

    // Set the confirm message
    const confirmMessageEl = document.getElementById('confirm-archive-file-message');
    if (confirmMessageEl) {
        confirmMessageEl.textContent = `Are you sure you want to unarchive this file?`;
    }

    // Store data for confirmation handler
    window.pendingUnarchiveType = window.currentArchiveType.toLowerCase();
    window.pendingUnarchiveDocketNo = window.currentArchiveDocketNo;
    window.pendingUnarchiveFileIndex = window.currentArchiveFileIndex;

    // Open confirmation
    window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'confirm-archive-file-modal' } }));

    // Attach event listener to the yes button if not already attached for file unarchive
    const confirmYesBtn = document.getElementById('confirm-archive-file-yes-btn');
    if (confirmYesBtn && !confirmYesBtn.dataset.fileUnarchiveListenerAttached) {
        confirmYesBtn.dataset.fileUnarchiveListenerAttached = 'true';
        confirmYesBtn.addEventListener('click', function fileUnarchiveHandler() {
            const type = window.pendingUnarchiveType;
            const docketNo = window.pendingUnarchiveDocketNo;
            const fileIndex = window.pendingUnarchiveFileIndex;
            const btn = document.getElementById('unarchive-archive-btn');
            if (btn) btn.disabled = true;

            // Close the modal
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-archive-file-modal' } }));

            // Proceed with unarchiving (original logic)
            fetch(`/records/${type}/${docketNo}/files/${fileIndex}/unarchive`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (window.showToast) {
                            window.showToast('File unarchived successfully!', 'success');
                        }
                        window.archiveShowFileList();
                        window.loadArchiveFileList(type, docketNo);
                        updateArchiveRowCount(type.toUpperCase(), docketNo);
                        window.currentArchiveFileIndex = undefined;
                    } else {
                        if (window.showToast) {
                            window.showToast(data.message || 'Failed to unarchive file.', 'error');
                        } else {
                            alert(data.message || 'Failed to unarchive file.');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error unarchiving file:', error);
                    if (window.showToast) {
                        window.showToast('An error occurred while unarchiving the file.', 'error');
                    } else {
                        alert('An error occurred while unarchiving the file.');
                    }
                })
                .finally(() => {
                    if (btn) btn.disabled = false;
                });
        });
    }
};

function updateArchiveRowCount(type, docketNo) {
    const row = document.querySelector(`.archive-row[data-type="${type}"][data-docket="${docketNo}"]`);
    if (!row) {
        return;
    }

    const countCell = row.querySelector('td:nth-child(4)');
    if (!countCell) {
        return;
    }

    const currentText = countCell.textContent.trim();
    const match = currentText.match(/(\d+)/);
    if (!match) {
        return;
    }

    let count = parseInt(match[1], 10);
    count = Math.max(0, count - 1);

    if (count === 0) {
        row.remove();
    } else {
        const suffix = count === 1 ? 'file' : 'files';
        countCell.textContent = `${count} ${suffix}`;
        row.dataset.file = `${count} ${suffix}`;
    }

    const noRecordsRow = document.getElementById('no-archived-records-row');
    const remainingRows = document.querySelectorAll('.archive-row').length;
    if (remainingRows === 0) {
        if (!noRecordsRow) {
            const tableBody = document.querySelector('#archiveTable tbody');
            if (tableBody) {
                const newNoRecordsRow = document.createElement('tr');
                newNoRecordsRow.id = 'no-archived-records-row';
                newNoRecordsRow.innerHTML = '<td class="px-6 py-4 text-center text-sm italic text-gray-500" colspan="7">No archived files found</td>';
                tableBody.appendChild(newNoRecordsRow);
            }
        } else {
            noRecordsRow.style.display = '';
        }
    }
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
    filePreview.onerror = function () {
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
        const type = window.currentArchiveType.toLowerCase();
        const url = `/records/${type}/${window.currentArchiveDocketNo}/download/${window.currentArchiveFileIndex}`;
        window.open(url, '_blank');
    }
}

// Load archived files list for the docket
window.loadArchiveFileList = function (type, docketNo) {
    const tbody = document.getElementById('archive-file-list-body');
    if (!tbody) return;

    // Show loading
    tbody.innerHTML = '<tr><td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Loading archived files...</td></tr>';

    fetch(`/records/${type}/${docketNo}/archive-files`)
        .then(response => response.json())
        .then(data => {
            window.renderArchiveFileList(data.files || []);
        })
        .catch(error => {
            console.error('Error loading archive files:', error);
            tbody.innerHTML = '<tr><td colspan="3" class="px-6 py-4 text-center text-sm text-red-500">Error loading files</td></tr>';
        });
};

// Render archived files list
window.renderArchiveFileList = function (files) {
    const tbody = document.getElementById('archive-file-list-body');
    if (!tbody) return;

    tbody.innerHTML = '';
    if (files.length === 0) {
        tbody.innerHTML = '<tr><td class="px-6 py-4 text-center text-sm italic text-gray-500" colspan="3">No archived files found for this docket</td></tr>';
        return;
    }

    files.forEach((file, index) => {
        const originalIndex = file.originalIndex !== undefined ? file.originalIndex : index;
        const row = document.createElement('tr');
        row.className = 'cursor-pointer hover:bg-gray-50 archive-file-row';
        row.setAttribute('data-file-index', originalIndex);
        row.innerHTML = `
            <td class="px-6 py-4 wrap-break-word text-sm font-medium text-gray-900 text-center">${file.name || 'N/A'}</td>
            <td class="px-6 py-4 wrap-break-word text-sm text-gray-500 text-center">${file.date_archived ? new Date(file.date_archived).toLocaleString() : 'N/A'}</td>
            <td class="px-6 py-4 wrap-break-word text-sm text-gray-500 text-center">${file.last_updated_by || 'N/A'}</td>
        `;
        tbody.appendChild(row);
    });

    // Delegate click for file rows (remove existing listener to prevent duplicates)
    const existingListener = tbody.__archiveFileClickHandler;
    if (existingListener) {
        tbody.removeEventListener('click', existingListener);
    }

    const clickHandler = function (e) {
        const row = e.target.closest('tr.archive-file-row');
        if (row) {
            const fileIndex = parseInt(row.dataset.fileIndex);
            window.pendingArchiveFileName = row.querySelector('td:first-child').textContent.trim();
            window.archiveLoadFilePreview(fileIndex);
        }
    };
    tbody.addEventListener('click', clickHandler);
    tbody.__archiveFileClickHandler = clickHandler;
};

// Show file list view (back button)
window.archiveShowFileList = function () {
    document.getElementById('archive-file-list-view').style.display = 'block';
    document.getElementById('archive-file-preview-view').style.display = 'none';
};

// Expose function to global scope for onclick handlers
window.exportArchiveFile = exportArchiveFile;

// Unarchive functionality is implemented in file-utils.js

document.addEventListener('DOMContentLoaded', function () {
    initArchiveSearch();
    initUnarchiveButtons();
    initArchiveModal();
    initArchiveFileListSearch();
});
