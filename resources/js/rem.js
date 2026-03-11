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

// =========================================
// Global Function Exports
// =========================================

window.openRemModal = openRemModal;
window.remGoBackToFileList = () => window.goBackToFileList('rem');
window.exportRemFile = () => exportFile('rem');
window.remShowFileList = () => window.showGenericFileList('rem');
window.loadRemFileList = (record) => window.loadGenericFileList('rem', record);
window.updateRemData = () => window.updateData('rem');
window.loadRemMunicipalities = loadRemMunicipalities;
window.loadRemProvinces = loadRemProvinces;
window.loadRemDropdowns = loadRemDropdowns;

// =========================================
// Utility Functions
// =========================================

/**
 * Creates and appends options to a select element
 * @param {HTMLSelectElement} select - The select element
 * @param {Array} items - Array of items with id and name properties
 * @param {string} defaultText - Default option text
 */
function populateSelectOptions(select, items, defaultText = 'Select Option') {
    if (!select) return;
    select.innerHTML = `<option value="">${defaultText}</option>`;
    items.forEach(item => {
        const option = document.createElement('option');
        option.value = item[`${select.id.includes('province') ? 'province' : 'municipality'}_id`];
        option.textContent = item[`${select.id.includes('province') ? 'province' : 'municipality'}_name`];
        select.appendChild(option);
    });
}

// =========================================
// Province/Municipality Dropdown Functions
// =========================================

async function loadRemProvinces() {
    try {
        const response = await fetch('/rem/provinces');
        const provinces = await response.json();
        const addRemProvince = document.getElementById('add-rem-province');
        if (addRemProvince) populateSelectOptions(addRemProvince, provinces, 'Select Province');
        return provinces;
    } catch (error) {
        console.error('Error loading provinces:', error);
        return [];
    }
}

async function loadRemMunicipalities(provinceId, targetSelect = 'add-rem-municipality') {
    const municipalitySelect = document.getElementById(targetSelect);
    if (!municipalitySelect) return;

    if (!provinceId) {
        populateSelectOptions(municipalitySelect, [], 'Select Municipality');
        municipalitySelect.disabled = true;
        return;
    }

    try {
        const response = await fetch(`/rem/municipalities?province_id=${provinceId}`);
        const municipalities = await response.json();
        populateSelectOptions(municipalitySelect, municipalities, 'Select Municipality');
        municipalitySelect.disabled = false;
    } catch (error) {
        console.error('Error loading municipalities:', error);
        municipalitySelect.innerHTML = '<option value="">Error loading</option>';
    }
}

async function loadRemDropdowns(record) {
    const provinceSelect = document.getElementById('rem-province');
    const municipalitySelect = document.getElementById('rem-municipality');
    if (!provinceSelect || !municipalitySelect) return;

    try {
        const provincesResponse = await fetch('/rem/provinces');
        const provinces = await provincesResponse.json();
        populateSelectOptions(provinceSelect, provinces, 'Select Province');

        const provinceId = record.province_id;
        if (provinceId) {
            provinceSelect.value = provinceId;
            const municipalitiesResponse = await fetch(`/rem/municipalities?province_id=${provinceId}`);
            const municipalities = await municipalitiesResponse.json();
            populateSelectOptions(municipalitySelect, municipalities, 'Select Municipality');

            if (record.municipality_id) municipalitySelect.value = record.municipality_id;
            municipalitySelect.disabled = true;
        }
    } catch (error) {
        console.error('Error loading dropdowns:', error);
    }
}

function setupRemCascadingDropdowns() {
    const addRemProvince = document.getElementById('add-rem-province');
    if (addRemProvince) {
        addRemProvince.addEventListener('change', () => loadRemMunicipalities(addRemProvince.value, 'add-rem-municipality'));
    }
}

// =========================================
// Modal Functions
// =========================================

function openRemModal(record) {
    openGenericModal(record, 'rem', REM_FIELD_CONFIG);
    window.currentRemRecord = record;
    loadRemDropdowns(record);

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
                loadRemDropdowns(window.currentRemRecord);
            };
        }

        provinceSelect?.addEventListener('change', async () => {
            const provinceId = provinceSelect.value;
            populateSelectOptions(municipalitySelect, [], 'Select Municipality');
            municipalitySelect.disabled = !provinceId;
            if (provinceId) {
                try {
                    const response = await fetch(`/rem/municipalities?province_id=${provinceId}`);
                    const municipalities = await response.json();
                    populateSelectOptions(municipalitySelect, municipalities, 'Select Municipality');
                } catch (error) {
                    console.error('Error fetching municipalities:', error);
                }
            }
        });
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
    window.currentProvince = province;
    showLoading(container);

    try {
        const response = await fetch(`/rem/folder/${province}`);
        if (!response.ok) throw new Error('Failed to load folder content');
        container.innerHTML = await response.text();
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
        noRecordsRow.style.display = visibleRows > 0 ? 'none' : '';
    };

    [searchInput, statusFilter].forEach(input => input?.addEventListener('input', filterRows));
    statusFilter?.addEventListener('change', filterRows);

    tableBody.addEventListener('click', (e) => {
        const row = e.target.closest('tr.data-row');
        if (!row) return;
        openRemModal(JSON.parse(row.dataset.record));
    });

    const addRemDocketBtn = container.querySelector('#addRemDocketBtn');
    if (addRemDocketBtn) {
        addRemDocketBtn.addEventListener('click', () => {
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
    filterRows();
}

function attachBackButton(container, originalHTML) {
    const backBtn = container.querySelector('#backToFolders');
    if (!backBtn) return;
    backBtn.addEventListener('click', () => {
        container.innerHTML = originalHTML;
        initFolderClicks();
    });
}

async function updateRemData() {
    window.updateData('rem');
}

// =========================================
// Initialization
// =========================================

document.addEventListener('DOMContentLoaded', () => {
    initFolderClicks();
    initAddRemRecordModal();
    setupRemCascadingDropdowns();
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
    // Validate quantity first (before remarks since quantity is positioned before remarks in the form)
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

    // Validate other required fields (excluding remarks)
    const fields = REM_VALIDATION_FIELDS
        .filter(f => !f.id.includes('remarks'))
        .map(f => ({ id: `${prefix}${f.id.replace('rem-', '')}`, name: f.name }));

    for (const field of fields) {
        const element = document.getElementById(field.id);
        if (!element || !element.value.trim()) {
            window.showToast(`${field.name} is required.`, 'error');
            element?.focus();
            return false;
        }
    }

    return true;
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
// Export Functions
// =========================================

function initRemExport() {
    const exportBtn = document.getElementById('exportRemBtn');
    const cancelBtn = document.getElementById('cancel-export-rem-btn');
    const submitBtn = document.getElementById('export-rem-submit-btn');
    const provinceSelect = document.getElementById('export-rem-province');
    const municipalitySelect = document.getElementById('export-rem-municipality');

    if (!exportBtn) return;

    // Open export modal
    exportBtn.addEventListener('click', () => {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'export-rem' } }));
    });

    // Close modal
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'export-rem' } }));
        });
    }

    // Province change handler
    if (provinceSelect) {
        provinceSelect.addEventListener('change', async () => {
            const provinceId = provinceSelect.value;
            municipalitySelect.innerHTML = '<option value="">All Municipalities</option>';
            municipalitySelect.disabled = !provinceId;

            if (provinceId) {
                try {
                    const response = await fetch(`/rem/municipalities?province_id=${provinceId}`);
                    const municipalities = await response.json();

                    municipalities.forEach(municipality => {
                        const option = document.createElement('option');
                        option.value = municipality.municipality_id;
                        option.textContent = municipality.municipality_name;
                        municipalitySelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Error fetching municipalities:', error);
                }
            }
        });
    }

    // Export submit
    if (submitBtn) {
        submitBtn.addEventListener('click', () => {
            const provinceId = provinceSelect?.value || '';
            const municipalityId = municipalitySelect?.value || '';
            
            // Build URL with query parameters
            let url = '/rem/export?';
            const params = new URLSearchParams();
            if (provinceId) params.append('province_id', provinceId);
            if (municipalityId) params.append('municipality_id', municipalityId);
            url += params.toString();

            // Download file
            window.location.href = url;
            
            // Close modal
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'export-rem' } }));
            window.showToast('Exporting REM records...', 'success');
        });
    }
}
