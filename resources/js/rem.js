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
        province: 'rem-province',
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
        const saveBtn = document.getElementById('rem-save-btn');
        const cancelBtn = document.getElementById('rem-cancel-btn');

        if (editBtn) editBtn.addEventListener('click', remEnterEditMode);
        if (saveBtn) saveBtn.addEventListener('click', remSaveEdit);
        if (cancelBtn) cancelBtn.addEventListener('click', remCancelEdit);
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
    updateGenericData('rem');
}

/**
 * Enters edit mode for the REM record.
 */
function remEnterEditMode() {
    // Store original values
    const editableFields = ['rem-status', 'rem-quantity', 'rem-remarks', 'rem-file-name'];
    const allFields = ['rem-docket-no', 'rem-project-name', 'rem-province', 'rem-status', 'rem-quantity', 'rem-remarks', 'rem-file-name'];
    window.remOriginalValues = {};
    allFields.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            window.remOriginalValues[id] = element.value;
            if (editableFields.includes(id)) {
                if (id === 'rem-status') {
                    element.removeAttribute('disabled');
                    element.classList.remove('bg-gray-100');
                    element.classList.add('bg-white');
                } else {
                    element.removeAttribute('readonly');
                }
            }
        }
    });

    // Show file name field and populate file name if a file is selected
    const fileNameField = document.getElementById('rem-file-name-field');
    const fileNameInput = document.getElementById('rem-file-name');
    if (fileNameField && fileNameInput) {
        fileNameField.style.display = 'block';

        if (window.currentFileIndex !== undefined) {
            // Get file name from the files array
            fetch(`/rem/${encodeURIComponent(window.currentRemRecord.docket_no)}/files`)
                .then(response => response.json())
                .then(data => {
                    const files = data.files || [];
                    const file = files.find(f => f.index == window.currentFileIndex);
                    if (file) {
                        fileNameInput.value = file.name;
                        window.remOriginalValues['rem-file-name'] = file.name;
                    }
                })
                .catch(error => {
                    console.error('Error fetching file name:', error);
                });
        } else {
            fileNameInput.value = '';
            window.remOriginalValues['rem-file-name'] = '';
        }
    }

    // Hide EDIT, EXPORT, and ARCHIVE, show SAVE and CANCEL
    document.getElementById('rem-edit-btn').style.display = 'none';
    document.getElementById('export-rem-btn').style.display = 'none';
    document.getElementById('archive-rem-btn').style.display = 'none';
    document.getElementById('rem-save-btn').style.display = 'inline-block';
    document.getElementById('rem-cancel-btn').style.display = 'inline-block';
}

/**
 * Saves the edited REM record.
 */
async function remSaveEdit() {
    const docketNo = document.getElementById('rem-docket-no').value;
    const formData = {
        docket_no: docketNo,
        project_name: document.getElementById('rem-project-name').value,
        province: document.getElementById('rem-province').value,
        status: document.getElementById('rem-status').value,
        quantity: document.getElementById('rem-quantity').value || null,
        remarks: document.getElementById('rem-remarks').value,
    };

    // Check if file name was changed
    const fileNameInput = document.getElementById('rem-file-name');
    const originalFileName = window.remOriginalValues['rem-file-name'];
    const newFileName = fileNameInput ? fileNameInput.value : null;

    let recordUpdated = false;
    let fileRenamed = false;

    try {
        // Update record first
        const recordResponse = await fetch(`/rem/${window.currentRemRecord.docket_no}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        });

        if (recordResponse.ok) {
            const result = await recordResponse.json();
            window.currentRemRecord = result.rem;
            recordUpdated = true;
        } else {
            const errorData = await recordResponse.json();
            window.showToast(errorData.message || 'Error updating record', 'error');
            return;
        }

        // Rename file if name changed
        if (newFileName && originalFileName && newFileName !== originalFileName && window.currentFileIndex !== undefined) {
            const renameResponse = await fetch(`/rem/${window.currentRemRecord.docket_no}/files/${window.currentFileIndex}/rename`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ new_name: newFileName })
            });

            if (renameResponse.ok) {
                fileRenamed = true;
            } else {
                const errorData = await renameResponse.json();
                window.showToast('Record updated but file rename failed: ' + (errorData.message || 'Error renaming file'), 'warning');
            }
        }

        // Show success message
        if (recordUpdated && fileRenamed) {
            window.showToast('REM record and file name updated successfully!', 'success');
        } else if (recordUpdated) {
            window.showToast('REM record updated successfully!', 'success');
        }

        // Hide file name field immediately after successful save
        const fileNameField = document.getElementById('rem-file-name-field');
        if (fileNameField) {
            fileNameField.style.display = 'none';
        }

        remExitEditMode();
        await updateRemData();

        // Refresh file list if file was renamed
        if (fileRenamed) {
            loadRemFileList(window.currentRemRecord);
        }

    } catch (error) {
        console.error('Error:', error);
        window.showToast('Error updating. Please try again.', 'error');
    }
}

/**
 * Cancels the edit and reverts to original values.
 */
function remCancelEdit() {
    // Revert values
    const fields = ['rem-docket-no', 'rem-project-name', 'rem-province', 'rem-status', 'rem-quantity', 'rem-remarks', 'rem-file-name'];
    fields.forEach(id => {
        const element = document.getElementById(id);
        if (element && window.remOriginalValues[id] !== undefined) {
            element.value = window.remOriginalValues[id];
            if (id === 'rem-status') {
                element.setAttribute('disabled', true);
                element.classList.add('bg-gray-100');
                element.classList.remove('bg-white');
            } else {
                element.setAttribute('readonly', true);
            }
        }
    });

    // Hide file name field
    const fileNameField = document.getElementById('rem-file-name-field');
    if (fileNameField) {
        fileNameField.style.display = 'none';
    }

    remExitEditMode();
}

/**
 * Exits edit mode.
 */
function remExitEditMode() {
    // Make fields readonly
    const fields = ['rem-docket-no', 'rem-project-name', 'rem-province', 'rem-status', 'rem-quantity', 'rem-remarks'];
    fields.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            if (id === 'rem-status') {
                element.setAttribute('disabled', true);
            } else {
                element.setAttribute('readonly', true);
            }
        }
    });

    // Hide SAVE and CANCEL, show EDIT, EXPORT, and ARCHIVE
    document.getElementById('rem-edit-btn').style.display = 'inline-block';
    document.getElementById('export-rem-btn').style.display = 'inline-block';
    document.getElementById('archive-rem-btn').style.display = 'inline-block';
    document.getElementById('rem-save-btn').style.display = 'none';
    document.getElementById('rem-cancel-btn').style.display = 'none';
}

// =========================================
// Initialization
// =========================================

document.addEventListener('DOMContentLoaded', () => {
    initFolderClicks();
    initAddRemRecordModal();

    // Add event listeners for edit functionality
    const editBtn = document.getElementById('rem-edit-btn');
    const saveBtn = document.getElementById('rem-save-btn');
    const cancelBtn = document.getElementById('rem-cancel-btn');

    if (editBtn) editBtn.addEventListener('click', remEnterEditMode);
    if (saveBtn) saveBtn.addEventListener('click', remSaveEdit);
    if (cancelBtn) cancelBtn.addEventListener('click', remCancelEdit);
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


