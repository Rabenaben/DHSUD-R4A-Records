// =========================================
// Shared File Handling Utilities for HOA and REM Records
// =========================================

// =========================================
// Global Function Exports
// =========================================

// Make functions global for external access
window.handleFileUpload = handleFileUpload;
window.handleConfirmSaveFile = handleConfirmSaveFile;
window.loadFilePreview = loadFilePreview;
window.setValue = setValue;
window.openRecordModal = openRecordModal;
window.goBackToFileList = goBackToFileList;
window.exportFile = exportFile;
window.exportAllFiles = exportAllFiles;
window.archiveFile = archiveFile;
window.openGenericModal = openGenericModal;
window.loadGenericFileList = loadGenericFileList;
window.renderGenericFileList = renderGenericFileList;
window.showGenericFilePreview = showGenericFilePreview;
window.showGenericFileList = showGenericFileList;
window.updateGenericData = updateGenericData;
window.updateGenericStatusCards = updateGenericStatusCards;
window.updateGenericTable = updateGenericTable;

// =========================================
// File List Modal Functions
// =========================================

/**
 * Opens the file list modal for a given record and type (hoa or rem).
 * @param {Object} record - The record data.
 * @param {string} type - The type of record ('hoa' or 'rem').
 */
function openFileListModal(record, type) {
    window.currentRecord = record;
    window.currentRecordType = type;

    // Set dynamic title
    const titleEl = document.getElementById('file-list-title');
    const recordName = type === 'hoa' ? record.hoa_name : record.project_name;
    titleEl.textContent = `${recordName} - Docket: ${record.docket_no}`;

    // Set tbody to loading state
    const tbody = document.getElementById('file-list-body');
    tbody.innerHTML = `
        <tr>
            <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                <div class="flex justify-center items-center">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
                    <span class="ml-2">Loading files...</span>
                </div>
            </td>
        </tr>
    `;

    // Open modal immediately
    window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'file-list' } }));

    // Fetch files from the database
    fetch(`/${type}/${record.docket_no}/files`)
        .then(response => response.json())
        .then(data => {
            const files = data.files || [];
            renderFileList(files, record, type);
        })
        .catch(error => {
            console.error('Error fetching files:', error);
            const files = [];
            renderFileList(files, record, type);
        });
}

/**
 * Renders the file list in the modal table body.
 * @param {Array} files - Array of file objects.
 * @param {Object} record - The record data.
 * @param {string} type - The type of record ('hoa' or 'rem').
 */
function renderFileList(files, record, type) {
    const tbody = document.getElementById('file-list-body');

    if (files.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                    No file uploaded yet
                </td>
            </tr>
        `;
    } else {
        tbody.innerHTML = files.map(f => `
            <tr class="cursor-pointer hover:bg-gray-50 file-row" data-file-index="${f.index}">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">${f.name}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">${new Date(f.date_added).toLocaleString()}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">${f.last_updated_by || 'Unknown'}</td>
            </tr>
        `).join('');

        // Delegate click for file rows
        tbody.addEventListener('click', function onFileClick(e) {
            const row = e.target.closest('tr.file-row');
            if (!row) return;

            const fileIndex = parseInt(row.dataset.fileIndex);
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'file-list' } }));

            if (type === 'hoa') {
                openHoaModal(record);
            } else if (type === 'rem') {
                openRemModal(record);
            }

            tbody.removeEventListener('click', onFileClick); // Remove listener to avoid duplicates
        });
    }

    // Add File Button
    const addFileBtn = document.getElementById('add-file-btn');
    if (addFileBtn) {
        addFileBtn.addEventListener('click', () => {
            // Set the docket_no in the hidden field
            const docketNoHidden = document.getElementById('docket-no-hidden');
            if (docketNoHidden) {
                docketNoHidden.value = record.docket_no;
            }
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'file-list' } }));
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'add-file' } }));
        });
    }


}

// =========================================
// File Upload Functions
// =========================================

/**
 * Handles file upload form submission.
 */
function handleFileUpload() {
    const addFileForm = document.getElementById('add-file-form');
    if (!addFileForm || addFileForm.dataset.listenerAdded) return;

    addFileForm.dataset.listenerAdded = 'true';

    addFileForm.addEventListener('submit', (e) => {
        e.preventDefault();
        // Open confirmation modal instead of direct upload
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'save-file' } }));
    });
}

/**
 * Handles the confirm save button for file upload.
 */
function handleConfirmSaveFile() {
    const confirmSaveBtn = document.getElementById('confirm-save-btn');
    if (!confirmSaveBtn || confirmSaveBtn.dataset.listenerAdded) return;

    confirmSaveBtn.dataset.listenerAdded = 'true';

    confirmSaveBtn.addEventListener('click', () => {
        const addFileForm = document.getElementById('add-file-form');
        const formData = new FormData(addFileForm);
        const docketNo = formData.get('docket_no');
        const type = window.currentRecordType;

        fetch(`/${type}/${docketNo}/upload-file`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.showToast(data.message, 'success');
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'save-file' } }));
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-file' } }));
                    // Refresh the file list in the current modal
                    if (window.currentRecord && window.currentRecordType) {
                        if (window.currentRecordType === 'hoa') {
                            window.loadHoaFileList(window.currentRecord);
                        } else if (window.currentRecordType === 'rem') {
                            window.loadRemFileList(window.currentRecord);
                        }
                    }
                } else {
                    window.showToast('Failed to upload files.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showToast('An error occurred while uploading the files.', 'error');
            });
    });
}

// =========================================
// Archive Functions
// =========================================

/**
 * Archives a file of the specified type and docket.
 * @param {string} type - The type of record ('hoa' or 'rem').
 * @param {string} docketNo - The docket number.
 * @param {number} fileIndex - The index of the file to archive.
 */
function archiveFile(type, docketNo, fileIndex) {
    // Set the confirm message
    const confirmMessageEl = document.getElementById('confirm-archive-file-message');
    if (confirmMessageEl) {
        confirmMessageEl.textContent = `Are you sure you want to archive this file?`;
    }

    // Store the type, docketNo, and fileIndex for later use
    window.pendingArchiveType = type;
    window.pendingArchiveDocketNo = docketNo;
    window.pendingArchiveFileIndex = fileIndex;

    // Open the confirmation modal
    window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'confirm-archive-file-modal' } }));

    // Attach event listener to the yes button if not already attached
    const confirmYesBtn = document.getElementById('confirm-archive-file-yes-btn');
    if (confirmYesBtn && !confirmYesBtn.dataset.listenerAttached) {
        confirmYesBtn.dataset.listenerAttached = 'true';
        confirmYesBtn.addEventListener('click', () => {
            const type = window.pendingArchiveType;
            const docketNo = window.pendingArchiveDocketNo;
            const fileIndex = window.pendingArchiveFileIndex;

            // Close the modal
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-archive-file-modal' } }));

            // Proceed with archiving
            fetch(`/records/${type}/${docketNo}/files/${fileIndex}/archive`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.showToast('File archived successfully!', 'success');
                        // Reload the file list in the current modal and switch to file list view
                        if (window.currentRecord) {
                            if (type === 'hoa') {
                                window.loadHoaFileList(window.currentRecord);
                            } else if (type === 'rem') {
                                window.loadRemFileList(window.currentRecord);
                            }
                            // Switch to file list view
                            document.getElementById(`${type}-file-list-view`).style.display = 'block';
                            document.getElementById(`${type}-file-preview-view`).style.display = 'none';
                            const fileActions = document.getElementById(`${type}-file-actions`);
                            if (fileActions) fileActions.style.display = 'none';
                            // Clear the file label
                            const labelId = type === 'hoa' ? 'hoa-file-label' : 'rem-file-label';
                            const labelElement = document.getElementById(labelId);
                            if (labelElement) labelElement.textContent = '';
                            // Exit file name edit mode to reset icons
                            window.exitFileNameEditMode(type);
                        }
                    } else {
                        window.showToast('Failed to archive file.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.showToast('An error occurred while archiving the file.', 'error');
                });
        });
    }
}

// =========================================
// Preview Functions
// =========================================

/**
 * Loads and displays the file preview for a given record and file index.
 * @param {Object} record - The record data.
 * @param {number} fileIndex - The index of the file.
 * @param {string} type - The type of record ('hoa' or 'rem').
 * @param {string} labelId - The ID of the label element.
 * @param {string} previewId - The ID of the preview element.
 * @param {string} placeholderId - The ID of the placeholder element.
 */
function loadFilePreview(record, fileIndex, type, labelId, previewId, placeholderId) {
    const fileLabel = document.getElementById(labelId);
    const filePreview = document.getElementById(previewId);
    const filePlaceholder = document.getElementById(placeholderId);

    // Show loading state immediately
    fileLabel.value = 'Loading...';
    filePreview.style.display = 'none';
    filePlaceholder.innerHTML = `
        <div class="flex flex-col items-center justify-center h-full">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mb-2"></div>
            <span class="text-gray-500">Loading preview...</span>
        </div>
    `;
    filePlaceholder.style.display = 'flex';

    if (fileIndex !== undefined && fileIndex !== null) {
        // Fetch the file URL
        fetch(`/${type}/${record.docket_no}/files`)
            .then(response => response.json())
            .then(data => {
                const files = data.files || [];
                const file = files.find(f => f.index == fileIndex);
                if (file) {
                    fileLabel.value = file.name;
                    filePreview.src = `/${type}/${record.docket_no}/preview/${fileIndex}`;
                    filePreview.style.display = 'block';
                    filePlaceholder.style.display = 'none';
                } else {
                    fileLabel.value = '';
                    filePreview.style.display = 'none';
                    filePlaceholder.innerHTML = `
                        <div class="flex flex-col items-center justify-center h-full">
                            <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-gray-500">No file selected</span>
                        </div>
                    `;
                    filePlaceholder.style.display = 'flex';
                }
            })
            .catch(error => {
                console.error('Error fetching file:', error);
                fileLabel.value = '';
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
            });
    } else {
        fileLabel.value = '';
        filePreview.style.display = 'none';
        filePlaceholder.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full">
                <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-gray-500">No file selected</span>
            </div>
        `;
        filePlaceholder.style.display = 'flex';
    }
}

// =========================================
// Utility Functions
// =========================================

/**
 * Sets the value of an element by ID.
 * @param {string} id - The element ID.
 * @param {*} value - The value to set.
 */
function setValue(id, value) {
    const el = document.getElementById(id);
    if (el) el.value = value ?? '';
}

/**
 * Generic function to open a record modal.
 * @param {string} type - The type of record ('hoa' or 'rem').
 * @param {Object} record - The record data.
 * @param {number} fileIndex - The index of the file.
 * @param {Object} fieldConfig - Configuration for field IDs.
 * @param {Array} previewIds - Array of preview element IDs.
 */
function openRecordModal(type, record, fileIndex, fieldConfig, previewIds) {
    // Store the record for back navigation
    window.currentRecord = record;
    // Store the current file index for export
    window.currentFileIndex = fileIndex;

    // Set field values using config
    Object.entries(fieldConfig).forEach(([key, id]) => {
        setValue(id, record[key] ?? '');
    });

    // Load file preview
    loadFilePreview(record, fileIndex, type, ...previewIds);

    // Open modal
    window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: type } }));

    // Attach archive button event
    const archiveBtn = document.getElementById(`archive-${type}-btn`);
    if (archiveBtn) {
        archiveBtn.addEventListener('click', () => archiveFile(type, record.docket_no, window.currentFileIndex));
    }
}

/**
 * Navigates back to the file list modal.
 * @param {string} type - The type of record ('hoa' or 'rem').
 */
function goBackToFileList(type) {
    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: type } }));
    if (window.currentRecord) {
        openFileListModal(window.currentRecord, type);
    }
}

/**
 * Exports the current file.
 * @param {string} type - The type of record ('hoa' or 'rem').
 */
function exportFile(type) {
    if (window.currentRecord && window.currentFileIndex !== undefined) {
        const url = `/${type}/${window.currentRecord.docket_no}/download/${window.currentFileIndex}`;
        window.open(url, '_blank');
    }
}

/**
 * Exports all files for a given record as a ZIP file.
 * @param {string} type - The type of record ('hoa' or 'rem').
 */
function exportAllFiles(type) {
    if (!window.currentRecord) {
        if (window.showToast) {
            window.showToast('No record selected', 'error');
        }
        return;
    }

    const docketNo = window.currentRecord.docket_no;
    const url = `/${type}/${docketNo}/export-all-files`;

    // Show loading toast (use 'default' instead of 'info' since Toast only supports success, error, default)
    if (window.showToast) {
        window.showToast('Preparing files for download...', 'default');
    }

    // Create a link and trigger download
    const link = document.createElement('a');
    link.href = url;
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// =========================================
// Initialization
// =========================================

// Initialize file upload handler if form exists
handleFileUpload();
handleConfirmSaveFile();

// Reset add-file form when modal opens
window.addEventListener('open-modal', (e) => {
    if (e.detail.name === 'add-file') {
        const form = document.getElementById('add-file-form');
        if (form) {
            form.reset();
            // Reset file display
            updateSelectedFilesDisplay();
            // Ensure file input is cleared by cloning and replacing
            const fileInput = document.getElementById('file-upload');
            if (fileInput) {
                const newFileInput = fileInput.cloneNode(true);
                fileInput.parentNode.replaceChild(newFileInput, fileInput);
                // Re-attach change listener to new input
                attachFileChangeListener(newFileInput);
            }
        }

        // Attach cancel button listener
        const cancelAddFileBtn = document.getElementById('cancel-add-file-btn');
        if (cancelAddFileBtn && !cancelAddFileBtn.dataset.listenerAttached) {
            cancelAddFileBtn.dataset.listenerAttached = 'true';
            cancelAddFileBtn.addEventListener('click', () => {
                window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-file' } }));
            });
        }
    }
});

// Function to update the display of selected files
function updateSelectedFilesDisplay() {
    const fileInput = document.getElementById('file-upload');
    const fileDisplay = document.getElementById('file-display');
    const selectedFilesDiv = document.getElementById('selected-files');

    if (fileDisplay && selectedFilesDiv) {
        const files = fileInput.files;
        if (files.length === 0) {
            fileDisplay.textContent = 'No files chosen';
            selectedFilesDiv.innerHTML = '';
        } else if (files.length === 1) {
            fileDisplay.textContent = files[0].name;
            selectedFilesDiv.innerHTML = '';
        } else {
            fileDisplay.textContent = `${files.length} files selected`;
            selectedFilesDiv.innerHTML = '<ul class="list-disc list-inside">' +
                Array.from(files).map(file => `<li>${file.name}</li>`).join('') +
                '</ul>';
        }
    }
}

// Function to attach file change listener
function attachFileChangeListener(fileInput) {
    fileInput.addEventListener('change', (e) => {
        updateSelectedFilesDisplay();
    });
}

// =========================================
// Generic Modal and File Handling Functions
// =========================================

/**
 * Generic function to open a modal for HOA or REM records.
 * @param {Object} record - The record data.
 * @param {string} type - The type of record ('hoa' or 'rem').
 * @param {Object} fieldConfig - Configuration for field IDs.
 * @param {Function} recordTransformer - Optional function to transform record data.
 */
function openGenericModal(record, type, fieldConfig, recordTransformer = null) {
    // Transform record if needed
    let transformedRecord = record;
    if (recordTransformer) {
        transformedRecord = recordTransformer(record);
    }

    // Store the record for back navigation
    window.currentRecord = record;
    window.currentRecordType = type;

    // Reset edit mode state when opening a new record
    window.resetEditModeState(type);

    // Set field values using config
    Object.entries(fieldConfig).forEach(([key, id]) => {
        setValue(id, transformedRecord[key] ?? '');
    });

    // Load file list
    loadGenericFileList(type, record);

    // Ensure the modal starts in file list view
    showGenericFileList(type);

    // Open modal
    window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: type } }));

    // Attach add file button event
    const addFileBtn = document.getElementById(`${type}-add-file-btn`);
    if (addFileBtn) {
        addFileBtn.addEventListener('click', () => {
            // Set the docket_no in the hidden field
            const docketNoHidden = document.getElementById('docket-no-hidden');
            if (docketNoHidden) {
                docketNoHidden.value = record.docket_no;
            }
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'add-file' } }));
        });
    }

    // Attach export all files button event
    const exportAllFilesBtn = document.getElementById(`${type}-export-all-files-btn`);
    if (exportAllFilesBtn) {
        exportAllFilesBtn.addEventListener('click', () => {
            exportAllFiles(type);
        });
    }
}

/**
 * Generic function to load file list for HOA or REM records.
 * @param {string} type - The type of record ('hoa' or 'rem').
 * @param {Object} record - The record data.
 */
function loadGenericFileList(type, record) {
    const tbodyId = `${type}-file-list-body`;
    const tbody = document.getElementById(tbodyId);

    // Show loading state
    tbody.innerHTML = `
        <tr>
            <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                <div class="flex justify-center items-center">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
                    <span class="ml-2">Loading files...</span>
                </div>
            </td>
        </tr>
    `;

    // Fetch files from the database
    fetch(`/${type}/${record.docket_no}/files`)
        .then(response => response.json())
        .then(data => {
            const files = data.files || [];
            renderGenericFileList(files, record, type);
        })
        .catch(error => {
            console.error('Error fetching files:', error);
            if (window.showToast) {
                window.showToast('Error loading files. Please try again.', 'error');
            }
            renderGenericFileList([], record, type);
        });
}

/**
 * Generic function to render file list for HOA or REM records.
 * @param {Array} files - Array of file objects.
 * @param {Object} record - The record data.
 * @param {string} type - The type of record ('hoa' or 'rem').
 */
function renderGenericFileList(files, record, type) {
    const tbodyId = `${type}-file-list-body`;
    const tbody = document.getElementById(tbodyId);

    if (files.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                    No file uploaded yet
                </td>
            </tr>
        `;
    } else {
        tbody.innerHTML = files.map(f => `
            <tr class="cursor-pointer hover:bg-gray-50 ${type}-file-row" data-file-index="${f.index}">
                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">${f.name}</td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">${new Date(f.date_added).toLocaleString()}</td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">${f.last_updated_by || 'Unknown'}</td>
            </tr>
        `).join('');

        // Delegate click for file rows
        tbody.addEventListener('click', function onGenericFileClick(e) {
            const row = e.target.closest(`tr.${type}-file-row`);
            if (!row) return;

            const fileIndex = parseInt(row.dataset.fileIndex);
            showGenericFilePreview(record, fileIndex, type);
        });
    }

    // Enable or disable the Export All Files button based on file count
    const exportAllFilesBtn = document.getElementById(`${type}-export-all-files-btn`);
    if (exportAllFilesBtn) {
        if (files.length > 0) {
            exportAllFilesBtn.disabled = false;
            exportAllFilesBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            exportAllFilesBtn.disabled = true;
            exportAllFilesBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }
}

/**
 * Generic function to show file preview for HOA or REM records.
 * @param {Object} record - The record data.
 * @param {number} fileIndex - The index of the file.
 * @param {string} type - The type of record ('hoa' or 'rem').
 */
function showGenericFilePreview(record, fileIndex, type) {
    // Store the current file index for export
    window.currentFileIndex = fileIndex;

    // Show pencil icon for file name editing (only for non-staff users)
    const editBtnId = type === 'hoa' ? 'hoa-edit-file-name-btn' : 'rem-edit-file-name-btn';
    const editBtn = document.getElementById(editBtnId);
    if (editBtn && window.userRole !== 'Staff') {
        editBtn.style.display = 'inline-block';
    }

    // Load file preview
    const labelId = type === 'hoa' ? 'hoa-file-label-preview' : 'rem-file-label-preview';
    const previewId = type === 'hoa' ? 'file-preview' : 'rem-file-preview';
    const placeholderId = type === 'hoa' ? 'file-placeholder' : 'rem-file-placeholder';
    loadFilePreview(record, fileIndex, type, labelId, previewId, placeholderId);

    // Switch to preview view
    document.getElementById(`${type}-file-list-view`).style.display = 'none';
    document.getElementById(`${type}-file-preview-view`).style.display = 'block';

    // Attach archive button event
    const archiveBtn = document.getElementById(`archive-${type}-btn`);
    if (archiveBtn) {
        archiveBtn.addEventListener('click', () => archiveFile(type, record.docket_no, window.currentFileIndex));
    }
}

/**
 * Generic function to show file list view for HOA or REM records.
 * @param {string} type - The type of record ('hoa' or 'rem').
 */
function showGenericFileList(type) {
    document.getElementById(`${type}-file-list-view`).style.display = 'block';
    document.getElementById(`${type}-file-preview-view`).style.display = 'none';
    // Clear the file label
    const labelId = type === 'hoa' ? 'hoa-file-label' : 'rem-file-label';
    const element = document.getElementById(labelId);
    if (element) element.textContent = '';
    // Exit file name edit mode to reset icons
    window.exitFileNameEditMode(type);
}

/**
 * Generic function to update data for HOA or REM records.
 * @param {string} type - The type of record ('hoa' or 'rem').
 */
async function updateGenericData(type) {
    try {
        const response = await fetch(`/${type}/updated-data`);
        const data = await response.json();

        // Update status cards
        updateGenericStatusCards(data.counts);

        // Update table if applicable
        if (type === 'hoa') {
            updateGenericTable(data.records, 'hoaRecordsTable', createHoaTableRow);
        }
        // REM might not need table update here, as it's folder-based
    } catch (error) {
        console.error(`Error updating ${type.toUpperCase()} data:`, error);
    }
}

/**
 * Generic function to update status cards.
 * @param {Object} counts - The updated counts.
 */
function updateGenericStatusCards(counts) {
    const cards = [
        { key: 'total', selector: '.status-card-total' },
        { key: 'onShelf', selector: '.status-card-onShelf' },
        { key: 'unavailable', selector: '.status-card-unavailable' },
        { key: 'borrowed', selector: '.status-card-borrowed' },
    ];

    cards.forEach(card => {
        const element = document.querySelector(card.selector);
        if (element) {
            const countElement = element.querySelector('h2');
            if (countElement) {
                countElement.textContent = counts[card.key];
            }
        }
    });
}

/**
 * Generic function to update table with new data.
 * @param {Array} records - The updated records.
 * @param {string} tableId - The ID of the table body.
 * @param {Function} rowCreator - Function to create table rows.
 */
function updateGenericTable(records, tableId, rowCreator) {
    const tableBody = document.getElementById(tableId);
    if (!tableBody) return;

    // Clear existing rows except the no records row
    const existingRows = tableBody.querySelectorAll('tr:not(#noRecordsRow)');
    existingRows.forEach(row => row.remove());

    const noRecordsRow = document.getElementById('noRecordsRow');
    if (records.length === 0) {
        if (noRecordsRow) noRecordsRow.classList.remove('hidden');
        return;
    }

    if (noRecordsRow) noRecordsRow.classList.add('hidden');

    // Add new rows
    records.forEach(record => {
        const row = rowCreator(record);
        tableBody.insertBefore(row, noRecordsRow);
    });
}

// Attach listener on page load
document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('file-upload');
    if (fileInput) attachFileChangeListener(fileInput);

    // Handle cancel save button
    const cancelSaveBtn = document.getElementById('cancel-save-btn');
    if (cancelSaveBtn) {
        cancelSaveBtn.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'save-file' } }));
        });
    }
});
