// =========================================
// Generic Record Utility Functions
// =========================================

// =========================================
// File Name Editing Functions
// =========================================

/**
 * Enters file name edit mode for a given prefix.
 * @param {string} prefix - The prefix for element IDs (e.g., 'hoa', 'rem').
 */
function enterFileNameEditMode(prefix) {
    const fileLabel = document.getElementById(`${prefix}-file-label-preview`);
    if (!fileLabel || window.currentFileIndex === undefined) return;

    // Store original file name
    window[`${prefix}OriginalFileName`] = fileLabel.value;

    // Make file name editable
    fileLabel.readOnly = false;
    fileLabel.classList.add('border', 'border-gray-300', 'rounded', 'px-2', 'py-1');
    fileLabel.focus();

    // Hide file edit actions and show save/cancel icons
    document.getElementById(`${prefix}-file-edit-actions`).style.display = 'none';
    document.getElementById(`${prefix}-file-name-save-icons`).style.display = 'flex';

    // Attach event listeners for save/cancel
    const saveBtn = document.getElementById(`${prefix}-save-file-name-icon`);
    const cancelBtn = document.getElementById(`${prefix}-cancel-file-name-icon`);

    if (saveBtn) {
        saveBtn.onclick = () => saveFileName(prefix);
    }
    if (cancelBtn) {
        cancelBtn.onclick = () => cancelFileNameEdit(prefix);
    }
}

/**
 * Saves the edited file name for a given prefix.
 * @param {string} prefix - The prefix for element IDs and record type.
 */
async function saveFileName(prefix) {
    const fileLabel = document.getElementById(`${prefix}-file-label-preview`);
    const newName = fileLabel.value.trim();

    if (!newName) {
        window.showToast('File name cannot be empty', 'error');
        return;
    }

    const record = window.currentRecord || window[`current${prefix.charAt(0).toUpperCase() + prefix.slice(1)}Record`];
    const endpoint = `/${prefix}/${record.docket_no}/files/${window.currentFileIndex}/rename`;

    try {
        const response = await fetch(endpoint, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ new_name: newName })
        });

        if (response.ok) {
            window.showToast('File renamed successfully!', 'success');
            exitFileNameEditMode(prefix);
            // Refresh file list
            window[`load${prefix.charAt(0).toUpperCase() + prefix.slice(1)}FileList`](record);
        } else {
            const errorData = await response.json();
            window.showToast(errorData.message || 'Error renaming file', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        window.showToast('Error renaming file. Please try again.', 'error');
    }
}

/**
 * Cancels file name editing and reverts changes for a given prefix.
 * @param {string} prefix - The prefix for element IDs.
 */
function cancelFileNameEdit(prefix) {
    const fileLabel = document.getElementById(`${prefix}-file-label-preview`);
    if (fileLabel && window[`${prefix}OriginalFileName`] !== undefined) {
        fileLabel.value = window[`${prefix}OriginalFileName`];
    }
    exitFileNameEditMode(prefix);
}

/**
 * Exits file name edit mode for a given prefix.
 * @param {string} prefix - The prefix for element IDs.
 */
function exitFileNameEditMode(prefix) {
    const fileLabel = document.getElementById(`${prefix}-file-label-preview`);
    if (fileLabel) {
        fileLabel.readOnly = true;
        fileLabel.classList.remove('border', 'border-gray-300', 'rounded', 'px-2', 'py-1');
    }

    // Hide save/cancel icons and show file edit actions (with null checks for staff users)
    const saveIcons = document.getElementById(`${prefix}-file-name-save-icons`);
    const fileEditActions = document.getElementById(`${prefix}-file-edit-actions`);
    if (saveIcons) saveIcons.style.display = 'none';
    if (fileEditActions) fileEditActions.style.display = 'flex';
}

// =========================================
// Record Edit Mode Functions
// =========================================

/**
 * Shows asterisks on all required labels in the modal.
 * @param {string} prefix - The prefix for element IDs.
 */
function showRequiredAsterisks(prefix) {
    // Find all required labels in the modal by looking for the label elements with required-label class
    // These are typically siblings or children of the input elements with the prefix in their ID
    const requiredLabels = document.querySelectorAll(`[for^="${prefix}-"], [for^="rem-"]`);

    requiredLabels.forEach(label => {
        // Check if this label has a required-label class or is inside a container with required-label class
        if (label.classList.contains('required-label')) {
            const asterisk = label.querySelector('.text-red-500');
            if (asterisk) {
                asterisk.style.display = 'inline';
            }
        }
        // Also check if label contains the asterisk directly
        const labelText = label.innerHTML;
        if (labelText.includes('text-red-500')) {
            // The asterisk span is already part of the label
            let asteriskSpan = label.querySelector('.text-red-500');
            if (!asteriskSpan) {
                // Create asterisk span if it doesn't exist as a child
                const asteriskMatch = labelText.match(/<span class="text-red-500">(\*)<\/span>/);
                if (asteriskMatch) {
                    asteriskSpan = label.querySelector('span');
                    if (asteriskSpan) {
                        asteriskSpan.style.display = 'inline';
                    }
                }
            } else {
                asteriskSpan.style.display = 'inline';
            }
        }
    });

    // Alternative approach: Find all labels that have the required attribute passed to input-required-mark
    // Look for the asterisk span directly by its class
    const allAsterisks = document.querySelectorAll('.text-red-500');
    allAsterisks.forEach(asterisk => {
        // Only affect asterisks in the modal (not in other parts of the page)
        const parentModal = asterisk.closest('.fixed, .relative, .absolute');
        if (parentModal) {
            asterisk.style.display = 'inline';
        }
    });
}

/**
 * Hides asterisks on all required labels in the modal.
 * @param {string} prefix - The prefix for element IDs.
 */
function hideRequiredAsterisks(prefix) {
    // Find all asterisk spans in the document and hide those in modals
    const allAsterisks = document.querySelectorAll('.text-red-500');
    allAsterisks.forEach(asterisk => {
        // Only affect asterisks in the modal area (not in other parts of the page)
        const parentLabel = asterisk.closest('label');
        if (parentLabel) {
            asterisk.style.display = 'none';
        }
    });
}

/**
 * Resets all asterisk spans to their default visible state.
 * This removes any inline display styles that may have been applied by hideRequiredAsterisks.
 */
function resetAllAsterisks() {
    const allAsterisks = document.querySelectorAll('.text-red-500');
    allAsterisks.forEach(asterisk => {
        // Remove the inline display style to restore default visibility
        asterisk.style.display = '';
    });
}

/**
 * Enters edit mode for a record with given prefix and field configurations.
 * @param {string} prefix - The prefix for element IDs.
 * @param {Array} editableFields - Array of field IDs that can be edited.
 * @param {Array} allFields - Array of all field IDs to store original values.
 */
function enterEditMode(prefix, editableFields, allFields) {
    // Store original values
    window[`${prefix}OriginalValues`] = {};
    allFields.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            window[`${prefix}OriginalValues`][id] = element.value;
            if (editableFields.includes(id)) {
                if (id.includes('status') || id.includes('province') || id.includes('municipality')) {
                    // Handle select dropdowns - remove disabled
                    element.removeAttribute('disabled');
                } else {
                    element.removeAttribute('readonly');
                }
            }
        }
    });

    // Hide EDIT button and show edit icons
    document.getElementById(`${prefix}-edit-btn`).style.display = 'none';
    document.getElementById(`${prefix}-edit-icons`).style.display = 'flex';

    // Show asterisks on required labels
    showRequiredAsterisks(prefix);
}

/**
 * Saves the edited record for a given prefix and form data builder.
 * @param {string} prefix - The prefix for element IDs and record type.
 * @param {Function} buildFormData - Function to build the form data object.
 * @param {Array} allFields - Array of all field IDs.
 * @param {Function} afterSave - Optional callback after successful save.
 */
async function saveEdit(prefix, buildFormData, allFields, afterSave = null) {
    const record = window.currentRecord || window[`current${prefix.charAt(0).toUpperCase() + prefix.slice(1)}Record`];
    const formData = buildFormData();

    try {
        // Update record
        const recordResponse = await fetch(`/${prefix}/${record.docket_no}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        });

        if (recordResponse.ok) {
            const result = await recordResponse.json();
            window.currentRecord = result[prefix];
            if (window[`current${prefix.charAt(0).toUpperCase() + prefix.slice(1)}Record`]) {
                window[`current${prefix.charAt(0).toUpperCase() + prefix.slice(1)}Record`] = result[prefix];
            }
            if (afterSave) afterSave();
            window.showToast(`${prefix.toUpperCase()} record updated successfully!`, 'success');
            exitEditMode(prefix, allFields);
            await window[`update${prefix.charAt(0).toUpperCase() + prefix.slice(1)}Data`]();
        } else {
            const errorData = await recordResponse.json();
            window.showToast(errorData.message || 'Error updating record', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        window.showToast('Error updating. Please try again.', 'error');
    }
}

/**
 * Cancels the edit and reverts to original values for a given prefix.
 * @param {string} prefix - The prefix for element IDs.
 * @param {Array} allFields - Array of all field IDs.
 */
function cancelEdit(prefix, allFields) {
    // Revert values
    allFields.forEach(id => {
        const element = document.getElementById(id);
        if (element && window[`${prefix}OriginalValues`][id] !== undefined) {
            element.value = window[`${prefix}OriginalValues`][id];
            if (id.includes('status')) {
                element.setAttribute('disabled', true);
            } else if (id.includes('province') || id.includes('municipality')) {
                // Handle province and municipality select dropdowns - re-disable
                element.setAttribute('disabled', true);
            } else {
                element.setAttribute('readonly', true);
            }
        }
    });

    exitEditMode(prefix, allFields);
}

/**
 * Exits edit mode for a given prefix.
 * @param {string} prefix - The prefix for element IDs.
 * @param {Array} allFields - Array of all field IDs.
 */
function exitEditMode(prefix, allFields) {
    // Make fields readonly
    allFields.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            if (id.includes('status')) {
                element.setAttribute('disabled', true);
            } else if (id.includes('province') || id.includes('municipality')) {
                // Handle select dropdowns (province and municipality) - disable
                element.setAttribute('disabled', true);
            } else {
                element.setAttribute('readonly', true);
            }
        }
    });

    // Hide edit icons and show edit button
    document.getElementById(`${prefix}-edit-icons`).style.display = 'none';
    document.getElementById(`${prefix}-edit-btn`).style.display = 'inline-block';

    // Show pencil icon for file name editing if file is selected
    if (window.currentFileIndex !== undefined) {
        document.getElementById(`${prefix}-edit-file-name-btn`).style.display = 'inline-block';
    }

    // Hide asterisks on required labels
    hideRequiredAsterisks(prefix);
}

/**
 * Resets the edit mode state for a given prefix when opening a new record.
 * This ensures the check/cross icons are hidden and fields are readonly.
 * @param {string} prefix - The prefix for element IDs (e.g., 'hoa', 'rem').
 */
function resetEditModeState(prefix) {
    const editIcons = document.getElementById(`${prefix}-edit-icons`);
    const editBtn = document.getElementById(`${prefix}-edit-btn`);

    // Hide edit icons and show edit button
    if (editIcons) editIcons.style.display = 'none';
    if (editBtn) editBtn.style.display = 'inline-block';

    // Hide asterisks on required labels (view mode)
    hideRequiredAsterisks(prefix);
}

// =========================================
// Update Data Functions
// =========================================

/**
 * Fetches updated data and updates the table and status cards for a given type.
 * @param {string} type - The record type ('hoa' or 'rem').
 */
async function updateData(type) {
    updateGenericData(type);
}

// =========================================
// Validation Functions
// =========================================

/**
 * Validates a form with given field configurations.
 * @param {Array} fields - Array of field objects with id, name, and optional min.
 * @returns {boolean} True if valid, false otherwise.
 */
function validateForm(fields) {
    for (let field of fields) {
        const value = document.getElementById(field.id).value.trim();
        if (!value) {
            window.showToast(`${field.name} is required.`, 'error');
            return false;
        }
        if (field.min && parseInt(value) < field.min) {
            window.showToast(`${field.name} must be at least ${field.min}.`, 'error');
            return false;
        }
    }
    return true;
}

// =========================================
// Generic Modal Initialization
// =========================================

/**
 * Initializes a generic modal for record editing with event listeners.
 * @param {string} prefix - The prefix for element IDs (e.g., 'hoa', 'rem').
 * @param {Array} editableFields - Array of field IDs that can be edited.
 * @param {Array} allFields - Array of all field IDs to store original values.
 * @param {Function} buildFormData - Function to build the form data object.
 * @param {Function} afterSave - Optional callback after successful save.
 */
function initGenericModal(prefix, editableFields, allFields, buildFormData, afterSave = null) {
    // Add event listeners for edit functionality
    const editBtn = document.getElementById(`${prefix}-edit-btn`);
    const saveBtn = document.getElementById(`${prefix}-save-btn`);
    const cancelBtn = document.getElementById(`${prefix}-cancel-btn`);

    if (editBtn) editBtn.addEventListener('click', () => window.enterEditMode(prefix, editableFields, allFields));
    if (saveBtn) saveBtn.addEventListener('click', () => window.saveEdit(prefix, buildFormData, allFields, afterSave));
    if (cancelBtn) cancelBtn.addEventListener('click', () => window.cancelEdit(prefix, allFields));
}

// =========================================
// Export Functions to Window
// =========================================

/**
 * Generic province/municipality dropdown loader.
 * @param {string} type - 'hoa' or 'rem'
 * @param {Object} record - Record data
 * @param {string} provinceSelectId - Province select ID
 * @param {string} municipalitySelectId - Municipality select ID
 */
async function loadProvinceMunicipalities(type, record, provinceSelectId, municipalitySelectId) {
    const provinceSelect = document.getElementById(provinceSelectId);
    const municipalitySelect = document.getElementById(municipalitySelectId);

    try {
        const provincesRes = await fetch(`/${type}/provinces`);
        const provinces = await provincesRes.json();

        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        provinces.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.province_id;
            opt.text = p.province_name;
            provinceSelect.appendChild(opt);
        });

        if (record.province_id) {
            provinceSelect.value = record.province_id;
            await loadMunicipalities(type, record.province_id, municipalitySelectId);
            if (record.municipality_id) municipalitySelect.value = record.municipality_id;
        }
    } catch (e) {
        console.error('Error loading dropdowns:', e);
    }
}

/**
 * Load municipalities for province.
 */
async function loadMunicipalities(type, provinceId, municipalitySelectId) {
    const muniSelect = document.getElementById(municipalitySelectId);
    muniSelect.innerHTML = '<option value="">Select Municipality</option>';
    muniSelect.disabled = !provinceId;

    if (!provinceId) return;

    try {
        const res = await fetch(`/${type}/municipalities?province_id=${provinceId}`);
        const munis = await res.json();
        munis.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.municipality_id;
            opt.text = m.municipality_name;
            muniSelect.appendChild(opt);
        });
    } catch (e) {
        console.error('Error loading municipalities:', e);
    }
}

/**
 * Setup cascading dropdown change handler.
 */
function setupCascadingDropdown(provinceSelectId, muniSelectId, type) {
    const provinceSelect = document.getElementById(provinceSelectId);
    if (!provinceSelect) return;

    provinceSelect.addEventListener('change', async () => {
        const provinceId = provinceSelect.value;
        await loadMunicipalities(type, provinceId, muniSelectId);
    });
}

/**
 * Generic validation for record fields.
 * @param {string} prefix - Field prefix ('', 'add-')
 * @param {Array} requiredFields - [{id, name}]
 * @returns {boolean}
 */
function validateRecord(prefix, requiredFields) {
    // Quantity validation (common)
    const qtyId = prefix + (prefix ? 'quantity' : 'quantity');
    const qtyEl = document.getElementById(qtyId);
    if (qtyEl && (!qtyEl.value.trim() || isNaN(qtyEl.value) || parseFloat(qtyEl.value) < 0)) {
        window.showToast('Quantity must be a valid non-negative number.', 'error');
        qtyEl.focus();
        return false;
    }

    // Required fields
    for (const field of requiredFields) {
        const el = document.getElementById(prefix + field.id);
        if (!el || !el.value.trim()) {
            window.showToast(`${field.name} is required.`, 'error');
            el?.focus();
            return false;
        }
    }
    return true;
}

/**
 * Prompts the archive docket confirmation modal and archives the docket on confirm.
 * @param {string} type - The record type ('hoa' or 'rem').
 * @param {string} docketNo - The docket number.
 * @param {string} recordModalName - The name of the record modal to close after archive.
 * @param {Function} updateCallback - Callback to refresh data after successful archive.
 */
function promptArchiveDocket(type, docketNo, recordModalName, updateCallback) {
    const confirmMessageEl = document.getElementById('confirm-archive-file-message');
    if (confirmMessageEl) {
        confirmMessageEl.textContent = `Are you sure you want to archive docket ${docketNo}? All files will be archived.`;
    }

    window.pendingArchiveType = type;
    window.pendingArchiveDocketNo = docketNo;
    window.pendingArchiveCloseModalName = recordModalName;
    window.pendingArchiveUpdateCallback = updateCallback;

    window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'confirm-archive-file-modal' } }));

    const confirmYesBtn = document.getElementById('confirm-archive-file-yes-btn');
    if (confirmYesBtn && !confirmYesBtn.dataset.archiveDocketListenerAttached) {
        confirmYesBtn.dataset.archiveDocketListenerAttached = 'true';
        confirmYesBtn.onclick = async () => {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-archive-file-modal' } }));

            try {
                const response = await fetch(`/records/${window.pendingArchiveType}/${window.pendingArchiveDocketNo}/archive-docket`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const data = await response.json();
                if (data.success) {
                    window.showToast('Docket archived successfully!', 'success');
                    if (window.pendingArchiveCloseModalName) {
                        window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: window.pendingArchiveCloseModalName } }));
                    }
                    if (typeof window.pendingArchiveUpdateCallback === 'function') {
                        await window.pendingArchiveUpdateCallback();
                    }
                } else {
                    window.showToast(data.message || 'Archive failed', 'error');
                }
            } catch (error) {
                console.error('Archive error:', error);
                window.showToast('Archive failed. Please try again.', 'error');
            }
        };
    }
}

/**
 * Attach modal edit/save/cancel listeners (replaces timeout manual code).
 */
function attachModalListeners(prefix, editableFields, allFields, buildFormData) {
    const editBtn = document.getElementById(prefix + '-edit-btn');
    const saveIcon = document.getElementById(prefix + '-save-icon');
    const cancelIcon = document.getElementById(prefix + '-cancel-icon');

    if (editBtn) {
        editBtn.onclick = () => window.enterEditMode(prefix, editableFields, allFields);
    }

    if (saveIcon) {
        saveIcon.onclick = () => {
            if (!window.validateRecord(prefix, [])) return; // Pass required fields config
            window.saveEdit(prefix, buildFormData, allFields);
        };
    }

    if (cancelIcon) {
        cancelIcon.onclick = () => window.cancelEdit(prefix, allFields);
    }
}

/**
 * Init add-record modal (generic).
 */
function initAddRecordModal(prefix, endpoint) {
    const formId = `add-record-form`; // TODO: make dynamic if needed
    const submitBtnId = `${prefix === 'hoa' ? 'add-record-submit-btn' : 'add-rem-record-submit-btn'}`;

    const submitBtn = document.getElementById(submitBtnId);
    if (submitBtn) {
        submitBtn.onclick = async () => {
            // Validate then confirm modal
            if (!window.validateRecord('add-', [])) return;
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'confirm-save-record-modal' } }));
        };
    }

    // Confirm save handler (shared)
    const confirmBtn = document.getElementById('confirm-save-record-yes-btn');
    if (confirmBtn) {
        confirmBtn.onclick = async () => {
            const form = document.getElementById(formId);
            const formData = new FormData(form);
            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await res.json();
                if (data.success) {
                    form.reset();
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-save-record-modal' } }));
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: formId.replace('-form', '') } }));
                    window.showToast(data.message || 'Record added!', 'success');
                    await window[`update${prefix.charAt(0).toUpperCase() + prefix.slice(1)}Data`]();
                }
            } catch (e) {
                window.showToast('Error adding record', 'error');
            }
        };
    }
}

// =========================================
// Archive/Unarchive Functions
// =========================================

/**
 * Unarchives all files for a given docket.
 * @param {string} type - The record type ('hoa' or 'rem').
 * @param {string} docketNo - The docket number.
 * @param {HTMLElement} button - The unarchive button element.
 */
function unarchiveDocket(type, docketNo, button) {
    button.disabled = true;
    button.textContent = 'Unarchiving...';

    fetch(`/records/${type}/${docketNo}/unarchive-docket`, {
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
                window.showToast('All files unarchived successfully!', 'success');

                // Check if table is empty and show no records message
                const tableBody = document.querySelector('#archiveTable tbody');
                const archiveRows = tableBody.querySelectorAll('.archive-row');
                const noRecordsRow = document.getElementById('no-archived-records-row');

                if (archiveRows.length === 0) {
                    if (noRecordsRow) {
                        noRecordsRow.style.display = '';
                    } else if (tableBody) {
                        const newNoRecordsRow = document.createElement('tr');
                        newNoRecordsRow.id = 'no-archived-records-row';
                        newNoRecordsRow.innerHTML = '<td class="px-6 py-4 text-center text-sm italic text-gray-500" colspan="7">No archived files found</td>';
                        tableBody.appendChild(newNoRecordsRow);
                    }
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

// Export loading overlays (moved from hoa/rem)
window.showExportLoading = function (type) {
    const overlay = document.getElementById(`export-loading-${type}`);
    if (overlay) overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
};

window.hideExportLoading = function (type) {
    const overlay = document.getElementById(`export-loading-${type}`);
    if (overlay) overlay.classList.add('hidden');
    document.body.style.overflow = '';
};

// Borrower page loading overlay
window.showBorrowerLoading = function () {
    const overlay = document.getElementById('borrower-loading-overlay');
    if (overlay) {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
};

window.hideBorrowerLoading = function () {
    const overlay = document.getElementById('borrower-loading-overlay');
    if (overlay) {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
        document.body.style.overflow = '';
    }
};

window.enterFileNameEditMode = enterFileNameEditMode;
window.saveFileName = saveFileName;
window.cancelFileNameEdit = cancelFileNameEdit;
window.exitFileNameEditMode = exitFileNameEditMode;
window.enterEditMode = enterEditMode;
window.saveEdit = saveEdit;
window.cancelEdit = cancelEdit;
window.exitEditMode = exitEditMode;
window.updateData = updateData;
window.validateForm = validateForm;
window.initGenericModal = initGenericModal;
window.resetEditModeState = resetEditModeState;
window.resetAllAsterisks = resetAllAsterisks;
window.loadProvinceMunicipalities = loadProvinceMunicipalities;
window.loadMunicipalities = loadMunicipalities;
window.setupCascadingDropdown = setupCascadingDropdown;
window.validateRecord = validateRecord;
window.attachModalListeners = attachModalListeners;
window.initAddRecordModal = initAddRecordModal;
window.promptArchiveDocket = promptArchiveDocket;
window.unarchiveDocket = unarchiveDocket;
