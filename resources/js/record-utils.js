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
                if (id.includes('status')) {
                    element.removeAttribute('disabled');
                    if (prefix === 'rem') {
                        element.classList.remove('bg-gray-100');
                        element.classList.add('bg-white');
                    }
                } else {
                    element.removeAttribute('readonly');
                }
            }
        }
    });

    // Hide EDIT button and show edit icons
    document.getElementById(`${prefix}-edit-btn`).style.display = 'none';
    document.getElementById(`${prefix}-edit-icons`).style.display = 'flex';
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
                if (prefix === 'rem') {
                    element.classList.add('bg-gray-100');
                    element.classList.remove('bg-white');
                }
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
