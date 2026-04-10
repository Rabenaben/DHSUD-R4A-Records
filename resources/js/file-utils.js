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

// Export locks - prevents multiple simultaneous exports
window.isExporting = false;
window.isSingleExporting = false;

window.openGenericModal = openGenericModal;
window.loadGenericFileList = loadGenericFileList;
window.renderGenericFileList = renderGenericFileList;
window.showGenericFilePreview = showGenericFilePreview;
window.showGenericFileList = showGenericFileList;
window.updateGenericData = updateGenericData;
window.updateGenericStatusCards = updateGenericStatusCards;
window.updateGenericTable = updateGenericTable;

// Global cache + request tracker
window.fileCache = window.fileCache || {};
window.previewRequestId = 0;

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

            // ✅ ADD THIS
            window.selectedFileIndex = fileIndex;

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

        // Show loading state
        confirmSaveBtn.disabled = true;
        confirmSaveBtn.innerHTML = `
            <span class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Uploading...
            </span>
        `;

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
            })
            .finally(() => {
                // Reset button
                confirmSaveBtn.disabled = false;
                confirmSaveBtn.innerHTML = 'Confirm';
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
        confirmYesBtn.onclick = () => {
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
        };
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

    const requestId = ++window.previewRequestId; // track latest request

    // Loading state
    if (fileLabel) fileLabel.value = 'Loading...';
    filePreview.style.display = 'none';
    filePlaceholder.innerHTML = `
        <div class="flex flex-col items-center justify-center h-full">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mb-2"></div>
            <span class="text-gray-500">Loading preview...</span>
        </div>
    `;
    filePlaceholder.style.display = 'flex';

    if (fileIndex === undefined || fileIndex === null) return;

    const cacheKey = `${type}-${record.docket_no}`;

    // ✅ Use cache if available
    const loadFromFiles = (files) => {
        // Ignore stale responses
        if (requestId !== window.previewRequestId) return;

        const file = files.find(f => f.index == fileIndex);

        if (file) {
            if (fileLabel) fileLabel.value = file.name;

            filePreview.src = `/${type}/${record.docket_no}/preview/${fileIndex}`;
            filePreview.style.display = 'block';
            filePlaceholder.style.display = 'none';
        } else {
            if (fileLabel) fileLabel.value = '';

            filePreview.style.display = 'none';
            filePlaceholder.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full">
                    <span class="text-gray-500">No file selected</span>
                </div>
            `;
            filePlaceholder.style.display = 'flex';
        }
    };

    // If cached → use it immediately
    if (window.fileCache[cacheKey]) {
        loadFromFiles(window.fileCache[cacheKey]);
        return;
    }

    // Otherwise fetch
    fetch(`/${type}/${record.docket_no}/files`)
        .then(res => res.json())
        .then(data => {
            const files = data.files || [];

            // Save to cache
            window.fileCache[cacheKey] = files;

            loadFromFiles(files);
        })
        .catch(err => {
            console.error('Error fetching file:', err);

            if (requestId !== window.previewRequestId) return;

            if (fileLabel) fileLabel.value = '';
            filePlaceholder.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full">
                    <span class="text-red-500">Failed to load preview</span>
                </div>
            `;
        });
}

// =========================================
// Utility Functions
// =========================================

/**
 * Creates a debounced version of a function.
 * @param {Function} func - The function to debounce.
 * @param {number} delay - Delay in milliseconds.
 * @returns {Function} Debounced function.
 */
function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

/**
 * Filters file rows by filename search term.
 * @param {string} type - Record type ('hoa' or 'rem').
 * @param {string} searchTerm - Search term to filter by.
 */
function filterFiles(type, searchTerm) {
    const rows = document.querySelectorAll(`.${type}-file-row`);
    const searchLower = searchTerm.toLowerCase().trim();

    rows.forEach(row => {
        const filenameCell = row.querySelector('td:first-child');
        if (filenameCell) {
            const filename = filenameCell.textContent.toLowerCase();
            if (searchLower === '' || filename.includes(searchLower)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}

/**
 * Initializes file list search for modals (HOA/REM) - mirrors archive.js logic.
 * Shows "No files found" row when no matches.
 * @param {string} type - Record type ('hoa' or 'rem').
 */
function initFileListSearch(type) {
    const searchInput = document.getElementById(`${type}-files-search`);
    const clearBtn = document.getElementById(`${type}-files-search-clear`);
    const tbody = document.getElementById(`${type}-file-list-body`);

    if (!searchInput || !tbody) return;

    const filterFileRows = () => {
        const searchValue = (searchInput.value || '').trim().toLowerCase();
        let visibleRows = 0;

        // Get original file count before filtering
        const originalFileCount = tbody.querySelectorAll(`tr.${type}-file-row`).length;

        // Filter data rows only
        tbody.querySelectorAll(`tr.${type}-file-row`).forEach(row => {
            const name = row.children[0]?.textContent?.toLowerCase() || '';
            const date = row.children[1]?.textContent?.toLowerCase() || '';
            const user = row.children[2]?.textContent?.toLowerCase() || '';
            const matches = [name, date, user].some(text => text.includes(searchValue));
            row.style.display = matches ? '' : 'none';
            if (matches) visibleRows++;
        });

        // Manage no-files row ONLY if there were originally files but none match search
        let noFilesRow = tbody.querySelector(`tr.no-${type}-files-row`);
        if (!noFilesRow) {
            noFilesRow = document.createElement('tr');
            noFilesRow.className = `no-${type}-files-row`;
            const td = document.createElement('td');
            td.colSpan = 3;
            td.className = 'px-6 py-4 text-center text-sm italic text-gray-500';
            td.textContent = 'No files found.';
            noFilesRow.appendChild(td);
            tbody.appendChild(noFilesRow);
        }

        // Show "No files found" ONLY when original files exist but visible rows = 0
        noFilesRow.style.display = (originalFileCount > 0 && visibleRows === 0) ? '' : 'none';
    };

    // Input handler
    const inputHandler = () => {
        filterFileRows();
        if (clearBtn) clearBtn.style.display = searchInput.value ? '' : 'none';
    };

    // Remove existing listeners
    searchInput.removeEventListener('input', window[`${type}SearchHandler`]);

    window[`${type}SearchHandler`] = inputHandler;
    searchInput.addEventListener('input', inputHandler);

    // Clear button handler
    if (clearBtn) {
        clearBtn.removeEventListener('click', window[`${type}ClearHandler`]);
        window[`${type}ClearHandler`] = () => {
            searchInput.value = '';
            filterFileRows();
            clearBtn.style.display = 'none';
        };
        clearBtn.addEventListener('click', window[`${type}ClearHandler`]);
    }

    // Initial filter
    filterFileRows();
}


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
        archiveBtn.onclick = () => archiveFile(type, record.docket_no, window.currentFileIndex);
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
    // Prevent multiple rapid calls
    if (window.isSingleExporting) {
        console.warn('Single file export already in progress');
        return;
    }

    if (window.currentRecord && window.currentFileIndex !== undefined) {
        window.isSingleExporting = true;
        const url = `/${type}/${window.currentRecord.docket_no}/download/${window.currentFileIndex}`;
        window.open(url, '_blank');
        
        // Reset lock after delay (download starts instantly)
        setTimeout(() => { 
            window.isSingleExporting = false; 
        }, 1500);
    }
}

/**
 * Exports all files for a given record as a ZIP file.
 * @param {string} type - The type of record ('hoa' or 'rem').
 */
function exportAllFiles(type) {
    // Prevent multiple calls
    if (window.isExporting) {
        console.warn('Export already in progress');
        return;
    }

    if (!window.currentRecord) {
        if (window.showToast) {
            window.showToast('No record selected', 'error');
        }
        return;
    }

    window.isExporting = true;

    const docketNo = window.currentRecord.docket_no;
    const url = `/${type}/${docketNo}/export-all-files`;

    // Show loading toast
    if (window.showToast) {
        window.showToast('Preparing ZIP download...', 'default');
    }

    // Single download trigger
    const link = document.createElement('a');
    link.href = url;
    link.download = `${type}_${docketNo}_files.zip`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Reset after delay (browser download starts instantly)
    setTimeout(() => { window.isExporting = false; }, 2000);
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

    // Disable archive button if the record is already archived
    const archiveDocketBtn = document.getElementById(`${type}-archive-docket-btn`);
    if (archiveDocketBtn) {
        const isArchived = String(transformedRecord.status || '').toUpperCase() === 'ARCHIVED';
        // Initial disable - will be updated after files load
        archiveDocketBtn.disabled = isArchived;
        archiveDocketBtn.title = isArchived ? 'This docket is already archived' : 'Archive Docket';
        archiveDocketBtn.classList.toggle('opacity-50', isArchived);
        archiveDocketBtn.classList.toggle('cursor-not-allowed', isArchived);
    }

    // Attach export button SAFELY (prevent duplicates)
    const exportAllFilesBtn = document.getElementById(`${type}-export-all-files-btn`);
    if (exportAllFilesBtn && !exportAllFilesBtn.dataset.exportListener) {
        exportAllFilesBtn.dataset.exportListener = 'attached';
        exportAllFilesBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            exportAllFiles(type);
        });
    }

    // Attach single file export onclick SAFELY (used in blade onclick="exportRemFile()")
    const singleExportBtns = document.querySelectorAll(`#export-${type}-btn, #export-hoa-btn`);
    singleExportBtns.forEach(btn => {
        if (!btn.dataset.singleExportListener) {
            btn.dataset.singleExportListener = 'attached';
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (typeof window[`export${type.charAt(0).toUpperCase() + type.slice(1)}File`] === 'function') {
                    window[`export${type.charAt(0).toUpperCase() + type.slice(1)}File`]();
                }
            });
        }
    });
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

    // Enable or disable Export All Files and Archive Docket buttons based on file count
    const exportAllFilesBtn = document.getElementById(`${type}-export-all-files-btn`);
    const archiveDocketBtn = document.getElementById(`${type}-archive-docket-btn`);

    // Export button - update state + ensure listener
    if (exportAllFilesBtn) {
        const hasFiles = files.length > 0;
        exportAllFilesBtn.disabled = !hasFiles;
        exportAllFilesBtn.classList.toggle('opacity-50', !hasFiles);
        exportAllFilesBtn.classList.toggle('cursor-not-allowed', !hasFiles);
        exportAllFilesBtn.title = hasFiles ? 'Export All Files' : 'No files to export';

        // Re-attach safely if needed
        if (!exportAllFilesBtn.dataset.exportListener) {
            exportAllFilesBtn.dataset.exportListener = 'attached';
            exportAllFilesBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                exportAllFiles(type);
            });
        }
    }

    // Archive docket button - disable if no files OR already archived
    if (archiveDocketBtn) {
        const recordStatus = String(record.status || '').toUpperCase();
        const isArchived = recordStatus === 'ARCHIVED';
        const noFiles = files.length === 0;
        const shouldDisable = isArchived || noFiles;

        archiveDocketBtn.disabled = shouldDisable;
        archiveDocketBtn.title = isArchived ? 'This docket is already archived' :
            noFiles ? 'Upload files first before archiving docket' :
                'Archive Docket';
        archiveDocketBtn.classList.toggle('opacity-50', shouldDisable);
        archiveDocketBtn.classList.toggle('cursor-not-allowed', shouldDisable);
    }

    if (files.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                    No file uploaded yet
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = files.map(f => `
        <tr class="cursor-pointer hover:bg-gray-50 ${type}-file-row" data-file-index="${f.index}">
            <td class="px-4 py-4 wrap-break-word text-sm font-medium text-gray-900 text-center">${f.name}</td>
            <td class="px-4 py-4 wrap-break-word text-sm text-gray-500 text-center">${new Date(f.date_added).toLocaleString()}</td>
            <td class="px-4 py-4 wrap-break-word text-sm text-gray-500 text-center">${f.last_updated_by || 'Unknown'}</td>
        </tr>
    `).join('');

    // Delegate click for file rows
    tbody.addEventListener('click', function onGenericFileClick(e) {
        const row = e.target.closest(`tr.${type}-file-row`);
        if (!row) return;

        const fileIndex = parseInt(row.dataset.fileIndex);
        showGenericFilePreview(record, fileIndex, type);
    });

    initFileListSearch(type);

    // Remove duplicate button logic (moved to top of renderGenericFileList)
    // Existing logic now at start of function before early return
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
        { key: 'archived', selector: '.status-card-archived' },
        { key: 'borrowed', selector: '.status-card-borrowed' },
    ];

    cards.forEach(card => {
        const element = document.querySelector(card.selector);
        if (element) {
            const countElement = element.querySelector('h2');
            if (countElement) {
                const value = Number(counts[card.key]);
                countElement.textContent = isNaN(value) ? counts[card.key] : value.toLocaleString();
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
