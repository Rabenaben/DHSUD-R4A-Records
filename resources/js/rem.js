// =========================================
// Constants
// =========================================

const REM_FIELDS = [
    'rem-docket-no', 'rem-project-name', 'rem-location',
    'rem-province', 'rem-municipality', 'rem-status',
    'rem-quantity', 'rem-remarks'
];

const REM_FIELD_CONFIG = {
    docket_no: 'rem-docket-no',
    project_name: 'rem-project-name',
    location: 'rem-location',
    province_id: 'rem-province',
    municipality_id: 'rem-municipality',
    status: 'rem-status',
    quantity: 'rem-quantity',
    remarks: 'rem-remarks'
};

const REM_VALIDATION_FIELDS = REM_FIELDS.map(id => ({
    id,
    name: id.replace(/rem-/g, '').replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}));

const REM_REQUIRED_FIELDS = [
    { id: 'docket-no', name: 'Docket No.' },
    { id: 'project-name', name: 'Project Name' },
    { id: 'location', name: 'Location' },
    { id: 'province', name: 'Province' },
    { id: 'municipality', name: 'Municipality' },
    { id: 'status', name: 'Status' }
];

// =========================================
// Global Function Exports
// =========================================

window.openRemModal = openRemModal;
window.remGoBackToFileList = () => window.goBackToFileList('rem');
window.exportRemFile = () => exportFile('rem');
window.remShowFileList = () => window.showGenericFileList('rem');
window.loadRemFileList = (record) => window.loadGenericFileList('rem', record);
window.updateRemData = () => window.updateData('rem');

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

// =========================================
// Modal Functions
// =========================================

function openRemModal(record) {
    window.previewRequestId++;

    openGenericModal(record, 'rem', REM_FIELD_CONFIG);
    window.currentRemRecord = record;
    window.loadProvinceMunicipalities('rem', record, 'rem-province', 'rem-municipality');

    setTimeout(() => {
        const editBtn = document.getElementById('rem-edit-btn');
        const saveIcon = document.getElementById('rem-save-icon');
        const cancelIcon = document.getElementById('rem-cancel-icon');
        const provinceSelect = document.getElementById('rem-province');
        const municipalitySelect = document.getElementById('rem-municipality');

        if (editBtn) editBtn.addEventListener('click', () => {
            window.enterEditMode('rem', REM_FIELDS, REM_FIELDS);
            provinceSelect?.removeAttribute('disabled');
            municipalitySelect?.removeAttribute('disabled');
        });

        if (saveIcon) {
            saveIcon.onclick = null;
            saveIcon.onclick = () => {
                if (!validateRemFields('rem-')) return;
                const buildFormData = () => ({
                    docket_no: document.getElementById('rem-docket-no').value,
                    project_name: document.getElementById('rem-project-name').value,
                    location: document.getElementById('rem-location').value,
                    province_id: document.getElementById('rem-province').value,
                    municipality_id: document.getElementById('rem-municipality').value,
                    status: document.getElementById('rem-status').value,
                    quantity: document.getElementById('rem-quantity').value || null,
                    remarks: document.getElementById('rem-remarks').value,
                });
                window.saveEdit('rem', buildFormData, REM_FIELDS, () => {
                    if (window.currentProvince) {
                        const folderContainer = document.getElementById('folderContainer');
                        if (folderContainer) loadFolderContent({ dataset: { province: window.currentProvince } }, folderContainer, window.originalFolderHTML);
                    }
                });
            };
        }

        if (cancelIcon) {
            cancelIcon.onclick = null;
            cancelIcon.onclick = () => {
                window.cancelEdit('rem', REM_FIELDS);
                provinceSelect?.setAttribute('disabled', true);
                municipalitySelect?.setAttribute('disabled', true);
                window.loadProvinceMunicipalities('rem', window.currentRemRecord, 'rem-province', 'rem-municipality');
            };
        }

        window.setupCascadingDropdown('rem-province', 'rem-municipality', 'rem');

        // Archive Docket button listener
        const archiveDocketBtn = document.getElementById('rem-archive-docket-btn');
        if (archiveDocketBtn) {
            archiveDocketBtn.addEventListener('click', () => {
                window.promptArchiveDocket('rem', record.docket_no, 'rem', async () => {
                    if (typeof updateRemData === 'function') {
                        await updateRemData();
                    }
                });
            });
        }

        if (window.selectedFileIndex !== undefined) {
            loadFilePreview(
                record,
                window.selectedFileIndex,
                'rem',
                'rem-file-label',
                'rem-file-preview',
                'rem-file-placeholder'
            );

            window.selectedFileIndex = undefined;
        }

    }, 100);
}

// =========================================
// Folder Management Functions
// =========================================

function initFolderClicks() {
    const folderContainer = document.getElementById('folderContainer');
    if (!folderContainer) return;
    window.originalFolderHTML = folderContainer.innerHTML;
    document.querySelectorAll('.folder').forEach(folder => {
        folder.addEventListener('click', () => loadFolderContent(folder, folderContainer, window.originalFolderHTML));
    });
}

async function loadFolderContent(folder, container, originalFolderHTML) {
    const province = folder.dataset.province;
    const provinceName = folder.dataset.provinceName;
    window.currentProvince = province;
    window.currentProvinceName = provinceName;
    showLoading(container);

    try {
        const response = await fetch(`/rem/folder/${province}`);
        if (!response.ok) throw new Error('Failed to load folder content');
        container.innerHTML = await response.text();
        const display = container.querySelector('#currentProvinceDisplay');
        if (display) {
            display.textContent = `Current Province: ${provinceName}`;
            display.classList.remove('hidden');
        }
        attachFilters(container);
        attachBackButton(container, originalFolderHTML);
    } catch (error) {
        console.error(error);
        container.innerHTML = '<div class="text-center py-6 text-red-600">Failed to load content. Please try again later.</div>';
    }
}

function showLoading(container) {
    container.innerHTML = '<div class="flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div><span class="ml-2 text-gray-600">Loading...</span></div>';
}

function attachFilters(container) {
    const searchInput = container.querySelector('#remSearchInput');
    const statusFilter = container.querySelector('#remStatusFilter');
    const municipalityFilter = container.querySelector('#remMunicipalityFilter');
    const tableBody = container.querySelector('#remTableBody');
    const noRecordsRow = container.querySelector('#noRemRecordsRow');

    if (!tableBody || !noRecordsRow) return;

    const filterRows = () => {
        const searchValue = (searchInput?.value || '').trim().toLowerCase();
        const statusValue = (statusFilter?.value || '').trim().toUpperCase();
        const municipalityValue = (municipalityFilter?.value || '').trim().toUpperCase();
        let visibleRows = 0;

        tableBody.querySelectorAll('.data-row').forEach(row => {
            const cells = Array.from(row.cells).map(cell => cell.textContent.trim());
            const docket = cells[0];
            const project = cells[1];
            const municipalityCell = cells[3];
            const statusCell = cells[4];
            const matchesSearch = [docket, project].some(text => text.toLowerCase().includes(searchValue));
            const matchesStatus = !statusValue || statusCell.toUpperCase() === statusValue;
            const matchesMunicipality = !municipalityValue || municipalityCell.toUpperCase() === municipalityValue;
            row.style.display = matchesSearch && matchesStatus && matchesMunicipality ? '' : 'none';
            if (matchesSearch && matchesStatus && matchesMunicipality) visibleRows++;
        });
        noRecordsRow.style.display = visibleRows > 0 ? 'none' : '';
    };

    [searchInput, statusFilter].forEach(input => input?.addEventListener('input', filterRows));
    statusFilter?.addEventListener('change', filterRows);
    municipalityFilter?.addEventListener('input', filterRows);
    municipalityFilter?.addEventListener('change', filterRows);

    tableBody.addEventListener('click', (e) => {
        const row = e.target.closest('tr.data-row');
        if (!row) return;
        openRemModal(JSON.parse(row.dataset.record));
    });

    const addRemDocketBtn = container.querySelector('#addRemDocketBtn');
    if (addRemDocketBtn) {
        addRemDocketBtn.addEventListener('click', () => {
            window.loadProvinceMunicipalities('rem', {}, 'add-rem-province', 'add-rem-municipality');
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'add-rem-record' } }));
            setTimeout(() => window.setupCascadingDropdown('add-rem-province', 'add-rem-municipality', 'rem'), 100);
        });
    }
    filterRows();
}

function attachBackButton(container, originalHTML) {
    const backBtn = container.querySelector('#backToFolders');
    if (!backBtn) return;
    backBtn.addEventListener('click', () => {
        const display = container.querySelector('#currentProvinceDisplay');
        if (display) {
            display.textContent = '';
            display.classList.add('hidden');
        }
        container.innerHTML = originalHTML;
        initFolderClicks();
        initRemExport();
    });
}

async function updateRemData() {
    if (window.currentProvince) {
        const container = document.getElementById('folderContainer');
        if (container) {
            showLoading(container);

            try {
                const response = await fetch(`/rem/folder/${window.currentProvince}`);
                if (!response.ok) {
                    throw new Error('Failed to refresh REM folder content');
                }

                container.innerHTML = await response.text();

                const display = container.querySelector('#currentProvinceDisplay');
                if (display) {
                    display.textContent = `Current Province: ${window.currentProvinceName || ''}`;
                    display.classList.remove('hidden');
                }

                attachFilters(container);
                attachBackButton(container, window.originalFolderHTML);
                initRemExport();
            } catch (error) {
                console.error(error);
            }
        }
    }

    if (typeof window.updateData === 'function') {
        window.updateData('rem');
    }
}

// =========================================
// Initialization
// =========================================

document.addEventListener('DOMContentLoaded', () => {
    initFolderClicks();
    initAddRemRecordModal();
    initRemExport();

    const editFileNameBtn = document.getElementById('rem-edit-file-name-btn');
    if (editFileNameBtn) editFileNameBtn.addEventListener('click', () => window.enterFileNameEditMode('rem'));

    window.addEventListener('close-modal', (e) => {
        if (e.detail.name === 'rem') {
            window.exitEditMode('rem', REM_FIELDS);
            window.resetEditModeState('rem');
            document.getElementById('rem-province')?.setAttribute('disabled', true);
            document.getElementById('rem-municipality')?.setAttribute('disabled', true);
        }
    });
});

// =========================================
// Validation Functions
// =========================================

function validateRemFields(prefix = '') {
    return window.validateRecord(prefix, REM_REQUIRED_FIELDS);
}

// =========================================
// Add Record Modal Functions
// =========================================

function initAddRemRecordModal() {
    const addRemRecordForm = document.getElementById('add-rem-record-form');
    const cancelAddRemRecordBtn = document.getElementById('cancel-add-rem-record-btn');
    const addRemRecordSubmitBtn = document.getElementById('add-rem-record-submit-btn');
    const confirmSaveBtn = document.getElementById('confirm-save-record-yes-btn');

    window.addEventListener('open-modal', (e) => {
        if (e.detail.name === 'add-rem-record') {
            window.loadProvinceMunicipalities('rem', {}, 'add-rem-province', 'add-rem-municipality');
            const addRemQuantityField = document.getElementById('add-rem-quantity');
            if (addRemQuantityField) addRemQuantityField.value = '0';
            window.resetAllAsterisks();
        }
    });

    if (cancelAddRemRecordBtn) {
        cancelAddRemRecordBtn.addEventListener('click', () => {
            if (addRemRecordForm) {
                addRemRecordForm.reset();
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
            if (!validateRemFields('add-rem-')) return;
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'confirm-save-record-modal' } }));
            setTimeout(() => {
                const confirmText = document.querySelector('#confirm-save-record-modal p');
                if (confirmText) confirmText.textContent = 'Are you sure you want to save this REM record?';
            }, 100);
        });
    }

    if (confirmSaveBtn) {
        confirmSaveBtn.addEventListener('click', async () => {
            const form = document.getElementById('add-rem-record-form');
            if (!form) return;

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const provinceInput = document.getElementById('add-rem-province');
            if (provinceInput?.disabled && provinceInput?.value) data.province = provinceInput.value;

            const municipalityInput = document.getElementById('add-rem-municipality');
            if (municipalityInput) data.municipality = municipalityInput.value;

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
                    form.reset();
                    const provinceSection = document.querySelector('#add-rem-record-form .province-section');
                    const provinceInputEl = document.getElementById('add-rem-province');
                    if (provinceSection) provinceSection.style.display = '';
                    if (provinceInputEl) provinceInputEl.disabled = false;

                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-save-record-modal' } }));
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-rem-record' } }));
                    window.showToast('REM record added successfully!', 'success');
                    await updateRemData();

                    if (window.currentProvince) {
                        const folderContainer = document.getElementById('folderContainer');
                        if (folderContainer) loadFolderContent({ dataset: { province: window.currentProvince } }, folderContainer, folderContainer.innerHTML);
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

// =========================================
// Export Functions (SIMPLIFIED)
// =========================================

function initRemExport() {
    const exportBtn = document.getElementById('exportRemBtn');

    if (!exportBtn) return;

    exportBtn.addEventListener('click', () => {
        if (exportBtn.dataset.addDocket === 'true') {
            // ✅ KEEP your custom "Add Record" behavior
            window.loadProvinceMunicipalities('rem', {}, 'add-rem-province', 'add-rem-municipality');
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'add-rem-record' } }));
            setTimeout(() => window.setupCascadingDropdown('add-rem-province', 'add-rem-municipality', 'rem'), 100);
        } else {
            // ✅ Open export modal (handled by export-utils.js)
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'export-rem' } }));
        }
    });

    // ✅ THIS LINE replaces ALL export logic
    window.initExport('rem');
}