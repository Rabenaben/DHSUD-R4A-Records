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
window.remShowFileList = function () { window.showGenericFileList('rem'); };
window.loadRemFileList = function (record) { window.loadGenericFileList('rem', record); };
window.updateRemData = updateRemData;

// =========================================
// Modal Functions
// =========================================

/**
 * Opens the REM modal for a specific record and loads the file list.
 * @param {Object} record - The record data.
 */
function openRemModal(record) {
    const fieldConfig = {
        docket_no: 'rem-docket-no',
        project_name: 'rem-project-name',
        location: 'rem-location',
        province: 'rem-province',
        municipality: 'rem-municipality',
        status: 'rem-status',
        quantity: 'rem-quantity',
        remarks: 'rem-remarks'
    };

    openGenericModal(record, 'rem', fieldConfig);

    // Store current record for edit functionality
    window.currentRemRecord = record;

    // Attach edit functionality event listeners after modal is opened
    setTimeout(() => {
        const editBtn = document.getElementById('rem-edit-btn');

        if (editBtn) editBtn.addEventListener('click', () => {
            const editableFields = ['rem-docket-no', 'rem-project-name', 'rem-location', 'rem-province', 'rem-municipality', 'rem-status', 'rem-quantity', 'rem-remarks'];
            const allFields = ['rem-docket-no', 'rem-project-name', 'rem-location', 'rem-province', 'rem-municipality', 'rem-status', 'rem-quantity', 'rem-remarks'];
            window.enterEditMode('rem', editableFields, allFields);
        });
    }, 100); // Small delay to ensure modal is rendered
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
    window.originalFolderHTML = originalFolderHTML;

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
        openRemModal(record);
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
// Update REM Data Function
// =========================================

/**
 * Fetches updated REM data and updates the table and status cards.
 */
async function updateRemData() {
    window.updateData('rem');
}

// =========================================
// Initialization
// =========================================

document.addEventListener('DOMContentLoaded', () => {
    initFolderClicks();
    initAddRemRecordModal();

    // Add event listeners for edit functionality
    const editBtn = document.getElementById('rem-edit-btn');
    const saveIcon = document.getElementById('rem-save-icon');
    const cancelIcon = document.getElementById('rem-cancel-icon');
    const editFileNameBtn = document.getElementById('rem-edit-file-name-btn');

        if (editBtn) editBtn.addEventListener('click', () => {
            const editableFields = ['rem-docket-no', 'rem-project-name', 'rem-location', 'rem-province', 'rem-municipality', 'rem-status', 'rem-quantity', 'rem-remarks'];
            const allFields = ['rem-docket-no', 'rem-project-name', 'rem-location', 'rem-province', 'rem-municipality', 'rem-status', 'rem-quantity', 'rem-remarks'];
            window.enterEditMode('rem', editableFields, allFields);
        });
    if (saveIcon) saveIcon.addEventListener('click', () => {
        // Validate fields before saving (quantity required, non-negative)
        if (!validateRemFields('rem-')) return;

        const buildFormData = () => ({
            docket_no: document.getElementById('rem-docket-no').value,
            project_name: document.getElementById('rem-project-name').value,
            location: document.getElementById('rem-location').value,
            province: document.getElementById('rem-province').value,
            municipality: document.getElementById('rem-municipality').value,
            status: document.getElementById('rem-status').value,
            quantity: document.getElementById('rem-quantity').value || null,
            remarks: document.getElementById('rem-remarks').value,
        });
        const allFields = ['rem-docket-no', 'rem-project-name', 'rem-location', 'rem-province', 'rem-municipality', 'rem-status', 'rem-quantity', 'rem-remarks'];
        window.saveEdit('rem', buildFormData, allFields, () => {
            // Reload current province folder after edit
            if (window.currentProvince) {
                const folderContainer = document.getElementById('folderContainer');
                if (folderContainer) {
                    loadFolderContent({ dataset: { province: window.currentProvince } }, folderContainer, window.originalFolderHTML);
                }
            }
        });
    });
    if (cancelIcon) cancelIcon.addEventListener('click', () => {
        const allFields = ['rem-docket-no', 'rem-project-name', 'rem-location', 'rem-province', 'rem-municipality', 'rem-status', 'rem-quantity', 'rem-remarks'];
        window.cancelEdit('rem', allFields);
    });
    if (editFileNameBtn) editFileNameBtn.addEventListener('click', () => window.enterFileNameEditMode('rem'));

    // Fix: Reset edit mode when REM modal is closed (without clicking cancel)
    window.addEventListener('close-modal', (e) => {
        if (e.detail.name === 'rem') {
            const allFields = ['rem-docket-no', 'rem-project-name', 'rem-location', 'rem-province', 'rem-municipality', 'rem-status', 'rem-quantity', 'rem-remarks'];
            window.exitEditMode('rem', allFields);
            window.resetEditModeState('rem');
        }
    });
});

// =========================================
// Validation Functions
// =========================================

/**
 * Validates REM record fields (for both add and edit operations).
 * @param {string} prefix - The prefix for field IDs (e.g., 'add-rem-' for add form, 'rem-' for edit modal).
 * @returns {boolean} True if valid, false otherwise.
 */
function validateRemFields(prefix = '') {
    const fields = [
        { id: `${prefix}docket-no`, name: 'Docket No' },
        { id: `${prefix}project-name`, name: 'Project Name' },
        { id: `${prefix}location`, name: 'Location' },
        { id: `${prefix}province`, name: 'Province' },
        { id: `${prefix}municipality`, name: 'Municipality' },
        { id: `${prefix}status`, name: 'Status' }
    ];

    // Check for empty required fields
    for (const field of fields) {
        const element = document.getElementById(field.id);
        if (!element || !element.value.trim()) {
            window.showToast(`${field.name} is required.`, 'error');
            element?.focus();
            return false;
        }
    }

    // Validate quantity is required and must be a valid non-negative number (allows 0, not negative)
    const quantityId = `${prefix}quantity`;
    const quantityElement = document.getElementById(quantityId);
    if (!quantityElement || !quantityElement.value.trim()) {
        window.showToast('Quantity is required.', 'error');
        quantityElement?.focus();
        return false;
    }
    const quantityValue = quantityElement.value.trim();
    if (isNaN(quantityValue) || parseFloat(quantityValue) < 0) {
        window.showToast('Quantity must be a valid non-negative number.', 'error');
        quantityElement.focus();
        return false;
    }

    return true;
}

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

    // Set default value of 0 for quantity field when opening add-rem-record modal
    window.addEventListener('open-modal', (e) => {
        if (e.detail.name === 'add-rem-record') {
            const addRemQuantityField = document.getElementById('add-rem-quantity');
            if (addRemQuantityField) {
                addRemQuantityField.value = '0';
            }
            // Reset all asterisks to visible state (fixes bug where asterisks stay hidden after exiting edit mode)
            window.resetAllAsterisks();
        }
    });

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
            // Validate form using REM-specific validation (quantity required, non-negative)
            if (!validateRemFields('add-rem-')) return;
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

            // Ensure municipality is included
            const municipalityInput = document.getElementById('add-rem-municipality');
            if (municipalityInput) {
                data.municipality = municipalityInput.value;
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
                    // Reset form after successful submission
                    form.reset();
                    // Re-enable and show province section
                    const provinceSection = document.querySelector('#add-rem-record-form .province-section');
                    const provinceInput = document.getElementById('add-rem-province');
                    if (provinceSection) provinceSection.style.display = '';
                    if (provinceInput) provinceInput.disabled = false;

                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-save-record-modal' } }));
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-rem-record' } }));
                    window.showToast('REM record added successfully!', 'success');
                    // Update status cards
                    await updateRemData();
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


