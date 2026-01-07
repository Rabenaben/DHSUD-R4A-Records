// =========================================
// Global Function Exports
// =========================================

// Make functions global for external access
window.openRemModal = openRemModal;
window.remGoBackToFileList = function () {
    window.goBackToFileList('rem');
};
window.exportRemFile = function () {
    exportFile('rem');
};

// =========================================
// Modal Functions
// =========================================

/**
 * Opens the REM modal for a specific record and file index.
 * @param {Object} record - The record data.
 * @param {number} fileIndex - The index of the file.
 */
function openRemModal(record, fileIndex) {
    const fieldConfig = {
        docket_no: 'rem-docket-no',
        project_name: 'rem-project-name',
        status: 'rem-status',
        quantity: 'rem-quantity',
        remarks: 'rem-remarks'
    };

    openRecordModal('rem', record, fileIndex, fieldConfig, ['rem-file-label', 'rem-file-preview', 'rem-file-placeholder']);
}

// =========================================
// Folder Management Functions
// =========================================

/**
 * Initializes click events for folder elements.
 */
function initFolderClicks() {
    const folderContainer = document.getElementById('folderContainer');
    if (!folderContainer) return;

    // Save original folder section HTML
    const originalFolderHTML = folderContainer.innerHTML;

    document.querySelectorAll('.folder').forEach(folder => {
        folder.addEventListener('click', () => loadFolderContent(folder, folderContainer, originalFolderHTML));
    });
}

/**
 * Loads folder content asynchronously for a given province.
 * @param {HTMLElement} folder - The folder element.
 * @param {HTMLElement} container - The container to update.
 * @param {string} originalFolderHTML - The original HTML to restore.
 */
async function loadFolderContent(folder, container, originalFolderHTML) {
    const province = folder.dataset.province;

    // Set current province for reload after save
    window.currentProvince = province;

    showLoading(container);

    try {
        const response = await fetch(`/rem/folder/${province}`);
        if (!response.ok) throw new Error('Failed to load folder content');

        const html = await response.text();
        container.innerHTML = html; // Replaces entire folder section

        attachFilters(container);
        attachBackButton(container, originalFolderHTML);
    } catch (error) {
        console.error(error);
        container.innerHTML = `
            <div class="text-center py-6 text-red-600">
                Failed to load content. Please try again later.
            </div>`;
    }
}

/**
 * Displays a loading spinner in the container.
 * @param {HTMLElement} container - The container to show loading in.
 */
function showLoading(container) {
    container.innerHTML = `
        <div class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            <span class="ml-2 text-gray-600">Loading...</span>
        </div>
    `;
}

/**
 * Attaches search and status filters to the table.
 * @param {HTMLElement} container - The container holding the table.
 */
function attachFilters(container) {
    const searchInput = container.querySelector('#remSearchInput');
    const statusFilter = container.querySelector('#remStatusFilter');
    const tableBody = container.querySelector('#remTableBody');
    const noRecordsRow = container.querySelector('#noRemRecordsRow');

    if (!tableBody || !noRecordsRow) return;

    const filterRows = () => {
        const searchValue = (searchInput?.value || '').trim().toLowerCase();
        const statusValue = (statusFilter?.value || '').trim().toUpperCase();

        let visibleRows = 0;

        tableBody.querySelectorAll('.data-row').forEach(row => {
            const [docket, project, statusCell] = Array.from(row.cells).map(cell => cell.textContent.trim());
            const matchesSearch = [docket, project].some(text => text.toLowerCase().includes(searchValue));
            const matchesStatus = !statusValue || statusCell.toUpperCase().includes(statusValue);

            row.style.display = matchesSearch && matchesStatus ? '' : 'none';
            if (matchesSearch && matchesStatus) visibleRows++;
        });

        // Toggle the empty row
        noRecordsRow.style.display = visibleRows > 0 ? 'none' : '';
    };

    [searchInput, statusFilter].forEach(input => input?.addEventListener('input', filterRows));
    [statusFilter].forEach(input => input?.addEventListener('change', filterRows));

    // Delegate click event for REM rows
    tableBody.addEventListener('click', (e) => {
        const row = e.target.closest('tr.data-row');
        if (!row) return;

        const record = JSON.parse(row.dataset.record);
        openFileListModal(record, 'rem');
    });

    // Add Docket Button Event Listener
    const addRemDocketBtn = container.querySelector('#addRemDocketBtn');
    if (addRemDocketBtn) {
        addRemDocketBtn.addEventListener('click', () => {
            // Hide province section and set province if we're in a folder
            const provinceSection = document.querySelector('#add-rem-record-form .province-section');
            const provinceInput = document.getElementById('add-rem-province');
            if (provinceSection && provinceInput && window.currentProvince) {
                provinceSection.style.display = 'none';
                provinceInput.value = window.currentProvince;
                provinceInput.disabled = true;
            }
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'add-rem-record' } }));
        });
    }

    filterRows(); // Run once on load
}

/**
 * Attaches back button functionality to restore original folder view.
 * @param {HTMLElement} container - The container to update.
 * @param {string} originalHTML - The original HTML to restore.
 */
function attachBackButton(container, originalHTML) {
    const backBtn = container.querySelector('#backToFolders');
    if (!backBtn) return;

    backBtn.addEventListener('click', () => {
        container.innerHTML = originalHTML; // Restore original folder section
        initFolderClicks(); // Reattach click events
    });
}

// =========================================
// Initialization
// =========================================

document.addEventListener('DOMContentLoaded', () => {
    initFolderClicks();
    initAddRemRecordModal();
});

// =========================================
// Add Record Modal Functions
// =========================================

/**
 * Initializes the add REM record modal functionality.
 */
function initAddRemRecordModal() {
    // Event listeners for modal buttons (these exist on page load)
    const addRemRecordForm = document.getElementById('add-rem-record-form');
    const cancelAddRemRecordBtn = document.getElementById('cancel-add-rem-record-btn');
    const addRemRecordSubmitBtn = document.getElementById('add-rem-record-submit-btn');
    const confirmSaveBtn = document.getElementById('confirm-save-record-yes-btn');

    if (cancelAddRemRecordBtn) {
        cancelAddRemRecordBtn.addEventListener('click', () => {
            if (addRemRecordForm) {
                addRemRecordForm.reset();
                // Re-enable and show province section when canceling
                const provinceSection = document.querySelector('#add-rem-record-form .province-section');
                const provinceInput = document.getElementById('add-rem-province');
                if (provinceSection) provinceSection.style.display = '';
                if (provinceInput) provinceInput.disabled = false;
            }
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-rem-record' } }));
        });
    }

    if (addRemRecordSubmitBtn) {
        addRemRecordSubmitBtn.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'confirm-save-record-modal' } }));
            // Update confirmation message for REM records after modal opens
            setTimeout(() => {
                const confirmText = document.querySelector('#confirm-save-record-modal p');
                if (confirmText) {
                    confirmText.textContent = 'Are you sure you want to save this REM record?';
                }
            }, 100);
        });
    }

    if (confirmSaveBtn) {
        confirmSaveBtn.addEventListener('click', async () => {
            const form = document.getElementById('add-rem-record-form');
            if (!form) return;

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Ensure province is included even if disabled
            const provinceInput = document.getElementById('add-rem-province');
            if (provinceInput && provinceInput.disabled && provinceInput.value) {
                data.province = provinceInput.value;
            }

            try {
                const response = await fetch('/rem', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-save-record-modal' } }));
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-rem-record' } }));
                    window.showToast('REM record added successfully!', 'success');
                    // Reload current province folder instead of full page
                    if (window.currentProvince) {
                        // Reload the current folder content
                        const folderContainer = document.getElementById('folderContainer');
                        if (folderContainer) {
                            loadFolderContent({ dataset: { province: window.currentProvince } }, folderContainer, folderContainer.innerHTML);
                        }
                    } else {
                        setTimeout(() => location.reload(), 1000);
                    }
                } else {
                    const errorData = await response.json();
                    window.showToast(errorData.message || 'Error adding record', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                window.showToast('Error adding record. Please try again.', 'error');
            }
        });
    }
}


