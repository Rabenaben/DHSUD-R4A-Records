// =========================================
// Global Function Exports
// =========================================

// Make functions global for external access
window.openHoaModal = openHoaModal;
window.hoaGoBackToFileList = function () {
    window.goBackToFileList('hoa');
};
window.exportHoaFile = function () {
    exportFile('hoa');
};
window.hoaShowFileList = function () { window.showGenericFileList('hoa'); };
window.loadHoaFileList = loadHoaFileList;
window.updateHoaData = updateHoaData;
window.createHoaTableRow = createHoaTableRow;

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
// Global Variables for Modals
// =========================================

let addRecordForm;
let municipalitySelect;

// =========================================
// Global Variables for Filtering
// =========================================

let activeRegionFilter = null;
let regionFilterStatus, filteredCountSpan, totalCountSpan, activeRegionSpan;

// =========================================
// Constants
// =========================================

const HOA_REQUIRED_FIELDS = [
    { id: 'hoa-id', name: 'HOA ID' },
    { id: 'docket-no', name: 'Docket No.' },
    { id: 'hoa-name', name: 'HOA Name' },
    { id: 'classification', name: 'Classification' },
    { id: 'hoa-status', name: 'HOA Status' },
    { id: 'location', name: 'Location' },
    { id: 'province', name: 'Province' },
    { id: 'municipality', name: 'Municipality' },
    { id: 'status', name: 'Status' }
];

/**
 * Opens the HOA modal for a specific record and loads the file list.
 * @param {Object} record - The record data.
 */
function openHoaModal(record) {
    const fieldConfig = {
        hoa_id: 'hoa-id',
        region: 'region',
        docket_no: 'docket-no',
        hoa_name: 'hoa-name',
        classification: 'classification',
        hoa_status: 'hoa-status',
        location: 'location',
        province: 'province',
        municipality: 'municipality',
        status: 'status',
        quantity: 'quantity',
        remarks: 'remarks'
    };

    // Handle nested province and municipality - now using dropdown values
    const recordTransformer = (rec) => ({
        ...rec,
        province: rec.province?.province_name ?? 'N/A',
        municipality: rec.municipality?.municipality_name ?? 'N/A'
    });

    openGenericModal(record, 'hoa', fieldConfig, recordTransformer);

    // Compute and set region based on docket_no
    let region = '';
    const docketNo = record.docket_no ? record.docket_no.toLowerCase() : '';
    if (docketNo.includes('riv')) {
        region = 'RIV';
    } else if (docketNo.includes('str')) {
        region = 'STR';
    } else if (docketNo.includes('ncr')) {
        region = docketNo.includes('n') ? 'NCR HOA N' : 'NCR HOA';
    } else if (docketNo.includes('r4a')) {
        region = 'R4A';
    } else {
        region = ''; // Default if no match
    }
    document.getElementById('region').value = region;

    // Load dropdowns after modal is opened
    window.loadProvinceMunicipalities('hoa', record, 'province', 'municipality');

    // Attach edit functionality event listeners after modal is opened
    setTimeout(() => {
        const editBtn = document.getElementById('hoa-edit-btn');
        const saveIcon = document.getElementById('hoa-save-icon');
        const cancelIcon = document.getElementById('hoa-cancel-icon');
        const editFileNameBtn = document.getElementById('hoa-edit-file-name-btn');
        const provinceSelect = document.getElementById('province');
        const municipalitySelect = document.getElementById('municipality');

        const editableFields = ['hoa-id', 'docket-no', 'hoa-name', 'classification', 'hoa-status', 'location', 'province', 'municipality', 'status', 'quantity', 'remarks'];
        const allFields = editableFields;

        const buildFormData = () => ({
            hoa_id: document.getElementById('hoa-id').value,
            docket_no: document.getElementById('docket-no').value,
            hoa_name: document.getElementById('hoa-name').value,
            classification: document.getElementById('classification').value,
            hoa_status: document.getElementById('hoa-status').value,
            location: document.getElementById('location').value,
            province_id: document.getElementById('province').value,
            municipality_id: document.getElementById('municipality').value,
            status: document.getElementById('status').value,
            quantity: document.getElementById('quantity').value || null,
            remarks: document.getElementById('remarks').value,
        });

        if (editBtn) editBtn.addEventListener('click', () => {
            window.enterEditMode('hoa', editableFields, allFields);
            provinceSelect.removeAttribute('disabled');
            municipalitySelect.removeAttribute('disabled');
        });

        if (saveIcon) {
            saveIcon.onclick = () => {
                if (!window.validateRecord('', HOA_REQUIRED_FIELDS)) return;
                window.saveEdit('hoa', buildFormData, allFields, updateHoaData);
            };
        }

        if (cancelIcon) cancelIcon.addEventListener('click', () => {
            window.cancelEdit('hoa', allFields);
            provinceSelect.setAttribute('disabled', true);
            municipalitySelect.setAttribute('disabled', true);
            window.loadProvinceMunicipalities('hoa', window.currentRecord, 'province', 'municipality');
        });

        if (editFileNameBtn) editFileNameBtn.addEventListener('click', () => window.enterFileNameEditMode('hoa'));

        window.setupCascadingDropdown('province', 'municipality', 'hoa');
    }, 100); // Small delay to ensure modal is rendered
}

/**
 * Loads the file list for HOA records.
 * @param {Object} record - The record data.
 */
function loadHoaFileList(record) {
    loadGenericFileList('hoa', record);
}

// =========================================
// Table Filtering Functions
// =========================================

/**
 * Attaches click event listeners to HOA table rows.
 */
function attachHoaRowClickListeners() {
    const tableBody = document.getElementById('hoaRecordsTable');
    if (!tableBody) return;

    // Delegate click event for HOA rows
    tableBody.addEventListener('click', (e) => {
        const row = e.target.closest('tr.hoa-row');
        if (!row) return;

        const record = JSON.parse(row.dataset.record);
        openHoaModal(record);
    });
}

/**
 * Initializes HOA records table with filtering and event listeners.
 */
function initHoaRecords() {
    // Declare modal elements
    addRecordForm = document.getElementById('add-record-form');
    municipalitySelect = document.getElementById('add-municipality');

    // Declare filter elements
    regionFilterStatus = document.getElementById('region-filter-status');
    filteredCountSpan = document.getElementById('filtered-count');
    totalCountSpan = document.getElementById('total-count');
    activeRegionSpan = document.getElementById('active-region');

    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const provinceFilter = document.getElementById('provinceFilter');
    const municipalityFilter = document.getElementById('municipalityFilter');
    const tableBody = document.getElementById('hoaRecordsTable');

    if (!searchInput || !statusFilter || !provinceFilter || !municipalityFilter || !tableBody) return;

    const getTableRows = () => Array.from(tableBody.querySelectorAll('tr.hoa-row'));

    /**
     * Filters municipality options based on the selected province.
     */
    const filterMunicipalities = () => {
        const selectedProvince = provinceFilter.value;
        const municipalityOptions = municipalityFilter.querySelectorAll('option');

        municipalityOptions.forEach(option => {
            if (option.value === '') {
                option.style.display = '';
                return;
            }
            const optionProvince = option.getAttribute('data-province');
            option.style.display = !selectedProvince || optionProvince === selectedProvince ? '' : 'none';
        });

        // Reset municipality if selected municipality is not in the filtered list
        if (municipalityFilter.value) {
            const selectedOption = municipalityFilter.querySelector(`option[value="${municipalityFilter.value}"]`);
            if (selectedOption && selectedOption.style.display === 'none') {
                municipalityFilter.value = '';
            }
        }
    };

    /**
     * Sets the province filter when a municipality is selected.
     */
    const setProvinceFromMunicipality = () => {
        const selectedMunicipality = municipalityFilter.value;
        if (selectedMunicipality) {
            const selectedOption = municipalityFilter.querySelector(`option[value="${selectedMunicipality}"]`);
            if (selectedOption) {
                const province = selectedOption.getAttribute('data-province');
                provinceFilter.value = province;
                filterMunicipalities(); // Re-filter to show only municipalities of this province
            }
        }
    };

    /**
     * Filters the table rows based on search input and filter selections by reloading data from server.
     */
    const filterTable = () => {
        // Reload the first page with current filters
        loadHoaPage(1);
    };

    // Region button event listeners
    const regionButtons = document.querySelectorAll('.region-btn');
    regionButtons.forEach(button => {
        button.addEventListener('click', () => {
            const region = button.dataset.region.toLowerCase();
            if (activeRegionFilter === region) {
                // Toggle off - clear filter
                activeRegionFilter = null;
                button.classList.remove('bg-blue-200', 'ring-2', 'ring-blue-500');
            } else {
                // Clear previous active button and set new active region
                regionButtons.forEach(btn => btn.classList.remove('bg-blue-200', 'ring-2', 'ring-blue-500'));
                activeRegionFilter = region;
                button.classList.add('bg-blue-200', 'ring-2', 'ring-blue-500');
            }
            // Reload the first page with current filters
            loadHoaPage(1);
        });
    });

    // Attach event listeners
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    provinceFilter.addEventListener('change', () => {
        filterMunicipalities();
        filterTable();
    });
    municipalityFilter.addEventListener('change', () => {
        setProvinceFromMunicipality();
        filterTable();
    });

    // Initial filter of municipalities
    filterMunicipalities();

    // Initialize region filter status
    filterTable();

    // Delegate click event for HOA rows
    tableBody.addEventListener('click', (e) => {
        const row = e.target.closest('tr.hoa-row');
        if (!row) return;

        const record = JSON.parse(row.dataset.record);
        openHoaModal(record);
    });

    // Add Docket Button Event Listener
    const addDocketBtn = document.getElementById('addDocketBtn');
    if (addDocketBtn) {
        addDocketBtn.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'add-record' } }));
        });
    }

    // Add Record Button Click with Validation and Confirmation
    const addRecordSubmitBtn = document.getElementById('add-record-submit-btn');
    if (addRecordSubmitBtn) {
        addRecordSubmitBtn.addEventListener('click', () => {
            // Validate form before opening confirmation modal using unified validation function
            if (!validateHoaFields('add-')) return;
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'confirm-save-record-modal' } }));
        });
    }

    // Reset form when opening add record modal
    window.addEventListener('open-modal', (e) => {
        if (e.detail.name === 'add-record') {
            addRecordForm.reset();
            municipalitySelect.disabled = true;
            window.loadProvinceMunicipalities('hoa', {}, 'add-province', 'add-municipality');
            // Set default value of 0 for quantity field
            const addQuantityField = document.getElementById('add-quantity');
            if (addQuantityField) {
                addQuantityField.value = '0';
            }
            // Reset all asterisks to visible state (fixes bug where asterisks stay hidden after exiting edit mode)
            window.resetAllAsterisks();
        }
    });

    // Confirm Save Record Modal Event Listener
    const confirmSaveBtn = document.getElementById('confirm-save-record-yes-btn');
    if (confirmSaveBtn) {
        confirmSaveBtn.addEventListener('click', async () => {
            const form = document.getElementById('add-record-form');
            const formData = new FormData(form);

            try {
                const response = await fetch('/hoa', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Reset form after successful submission
                    form.reset();
                    const municipalitySelect = document.getElementById('add-municipality');
                    if (municipalitySelect) municipalitySelect.disabled = true;

                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-save-record-modal' } }));
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-record' } }));
                    window.showToast(data.message || 'HOA record added successfully!', 'success');
                    await updateHoaData();
                } else {
                    window.showToast(data.message || 'Error adding record', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                window.showToast('Error adding record. Please try again.', 'error');
            }
        });
    }
}

// =========================================
// Add Record Modal Functions
// =========================================

/**
 * Initializes the add record modal functionality.
 */
function initAddRecordModal() {
    const addDocketBtn = document.getElementById('addDocketBtn');
    const addRecordForm = document.getElementById('add-record-form');
    const cancelAddRecordBtn = document.getElementById('cancel-add-record-btn');
    const provinceSelect = document.getElementById('add-province');
    const municipalitySelect = document.getElementById('add-municipality');

    if (!addDocketBtn || !addRecordForm) return;

    // Open modal
    addDocketBtn.addEventListener('click', () => {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'add-record' } }));
    });

    // Close modal
    cancelAddRecordBtn.addEventListener('click', () => {
        addRecordForm.reset();
        municipalitySelect.disabled = true;
        window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-record' } }));
    });

    // Province change handler
    window.setupCascadingDropdown('add-province', 'add-municipality', 'hoa');

    // Form submission
    addRecordForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(addRecordForm);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('/hoa', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error('HTTP error ' + response.status);
            }

            const result = await response.json();

            if (result.success) {
                // Close modal
                addRecordForm.reset();
                municipalitySelect.disabled = true;
                window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-record' } }));

                // Reload the page to show the new record
                location.reload();

                // Show success toast
                window.showToast(result.message, 'success');
            } else {
                window.showToast(result.message || 'Unknown error', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            window.showToast('Error saving record. Please try again.', 'error');
        }
    });
}

// =========================================
// Update HOA Data Function
// =========================================

/**
 * Fetches updated HOA data and updates the table and status cards.
 * Uses pagination to avoid loading all records at once.
 */
async function updateHoaData() {
    // Get the current page from the input or default to 1
    const pageInput = document.getElementById('hoa-page-input');
    const currentPage = pageInput ? parseInt(pageInput.value) : 1;

    // Reload only the current page (this is efficient)
    loadHoaPage(currentPage);

    // Also update status counts (this is lightweight)
    try {
        const response = await fetch('/hoa/updated-data');
        const data = await response.json();

        // Update status cards
        const totalEl = document.querySelector('.status-card-total h2');
        const onShelfEl = document.querySelector('.status-card-onShelf h2');
        const unavailableEl = document.querySelector('.status-card-unavailable h2');
        const borrowedEl = document.querySelector('.status-card-borrowed h2');

        if (totalEl) totalEl.textContent = data.counts.total;
        if (onShelfEl) onShelfEl.textContent = data.counts.onShelf;
        if (unavailableEl) unavailableEl.textContent = data.counts.unavailable;
        if (borrowedEl) borrowedEl.textContent = data.counts.borrowed;
    } catch (error) {
        console.error('Error updating HOA status cards:', error);
    }
}

/**
 * Creates a table row for an HOA record.
 * @param {Object} record - The record data.
 * @returns {HTMLElement} The table row element.
 */
function createHoaTableRow(record) {
    const statusClasses = {
        'ON-SHELF': 'bg-green-100 text-green-800',
        'BORROWED': 'bg-yellow-100 text-yellow-800',
        'DEFAULT': 'bg-red-100 text-red-800',
    };

    const statusClass = statusClasses[record.status] || statusClasses['DEFAULT'];

    const row = document.createElement('tr');
    row.className = 'hoa-row cursor-pointer transition hover:bg-blue-100';
    row.setAttribute('data-record', JSON.stringify(record));

    // Add data attributes for filtering
    ['docket_no', 'hoa_name', 'location', 'province', 'municipality', 'remarks', 'status'].forEach(col => {
        let value = '';
        if (col === 'province') {
            value = record.province?.province_name || '';
        } else if (col === 'municipality') {
            value = record.municipality?.municipality_name || '';
        } else {
            value = record[col] || '';
        }
        // Preserve uppercase for region, lowercase for others
        const finalValue = col === 'region' ? value.toUpperCase() : value.toLowerCase();
        row.setAttribute(`data-${col}`, finalValue);
    });

    row.innerHTML = `
        <td class="w-1/6 px-6 py-4 text-center text-sm text-gray-900">${record.docket_no}</td>
        <td class="w-1/3 px-6 py-4 text-center text-sm text-gray-900">${record.hoa_name}</td>
        <td class="w-1/6 px-6 py-4 text-center text-sm text-gray-900">${record.location}</td>
        <td class="w-1/6 px-6 py-4 text-center text-sm text-gray-900">${record.province?.province_name || 'N/A'}</td>
        <td class="w-1/6 px-6 py-4 text-center text-sm text-gray-900">${record.municipality?.municipality_name || 'N/A'}</td>
        <td class="w-1/6 px-6 py-4 text-center text-sm">
            <span class="${statusClass} inline-flex rounded-full px-2 py-1 text-xs font-semibold">
                ${record.status}
            </span>
        </td>
    `;

    return row;
}

// =========================================
// Validation Functions
// =========================================

/**
 * Validates HOA record fields (for both add and edit operations).
 * @param {string} prefix - The prefix for field IDs (e.g., 'add-' for add form, '' for edit modal).
 * @returns {boolean} True if valid, false otherwise.
 */
function validateHoaFields(prefix = '') {
    return window.validateRecord(prefix, HOA_REQUIRED_FIELDS);
}

// =========================================
// Initialization
// =========================================

/**
 * Attaches event listeners for HOA pagination elements.
 */
function attachHoaPaginationListeners() {
    const prevBtn = document.getElementById('hoa-prev-page');
    const nextBtn = document.getElementById('hoa-next-page');
    const pageInput = document.getElementById('hoa-page-input');

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            const currentPage = parseInt(pageInput.value);
            if (currentPage > 1) {
                loadHoaPage(currentPage - 1);
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            const currentPage = parseInt(pageInput.value);
            const maxPage = parseInt(pageInput.max);
            if (currentPage < maxPage) {
                loadHoaPage(currentPage + 1);
            }
        });
    }

    if (pageInput) {
        pageInput.addEventListener('change', function () {
            const page = parseInt(this.value);
            const min = parseInt(this.min);
            const max = parseInt(this.max);
            if (page >= min && page <= max) {
                loadHoaPage(page);
            } else {
                // Reset to current page if invalid
                this.value = this.dataset.currentPage || 1;
            }
        });

        pageInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                this.blur(); // Trigger change event
            }
        });
    }
}

/**
 * Loads a specific page via AJAX with current filter parameters.
 * @param {number} page - The page number to load.
 */
function loadHoaPage(page) {
    // Get current filter values
    const search = document.getElementById('searchInput')?.value || '';
    const status = document.getElementById('statusFilter')?.value || '';
    const province = document.getElementById('provinceFilter')?.value || '';
    const municipality = document.getElementById('municipalityFilter')?.value || '';
    const region = activeRegionFilter || '';

    // Build query parameters
    const params = new URLSearchParams({
        page: page,
        search: search,
        status: status,
        province: province,
        municipality: municipality,
        region: region
    });

    fetch('/hoa_records/ajax?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
        .then(response => response.json())
        .then(data => {
            // Replace the entire records component
            document.getElementById('hoa-records-component').innerHTML = data.table_html;

            // Check if there are any records and toggle the "No HOA records found" message
            const tableBody = document.getElementById('hoaRecordsTable');
            const noRecordsRow = document.getElementById('noRecordsRow');
            if (tableBody && noRecordsRow) {
                const hasRecords = tableBody.querySelectorAll('.hoa-row').length > 0;
                noRecordsRow.style.display = hasRecords ? 'none' : 'table-row';
            }

            // Reattach click listeners to the new table rows
            attachHoaRowClickListeners();

            // Reattach pagination listeners
            attachHoaPaginationListeners();
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            alert('Error loading records. Please try again.');
        });
}

// HOA Records AJAX Pagination (legacy for any remaining links)
document.addEventListener('click', function (e) {
    if (e.target.closest('#hoa-pagination-container a')) {
        e.preventDefault();
        const link = e.target.closest('#hoa-pagination-container a');
        const url = link.getAttribute('href');

        // Extract page number from URL
        const urlParams = new URL(url, window.location.origin);
        const page = urlParams.searchParams.get('page');

        loadHoaPage(page);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    initHoaRecords();
    initAddRecordModal();
    attachHoaRowClickListeners();
    attachHoaPaginationListeners();
    window.initExport('hoa');
    
    // Fix: Reset edit mode when HOA modal is closed (without clicking cancel)
    window.addEventListener('close-modal', (e) => {
        if (e.detail.name === 'hoa') {
            const allFields = ['hoa-id', 'docket-no', 'hoa-name', 'classification', 'hoa-status', 'location', 'province', 'municipality', 'status', 'quantity', 'remarks'];
            window.exitEditMode('hoa', allFields);
            window.resetEditModeState('hoa');

            // Also re-disable province and municipality dropdowns
            const provinceSelect = document.getElementById('province');
            const municipalitySelect = document.getElementById('municipality');
            if (provinceSelect) provinceSelect.setAttribute('disabled', true);
            if (municipalitySelect) municipalitySelect.setAttribute('disabled', true);
        }
    });
});