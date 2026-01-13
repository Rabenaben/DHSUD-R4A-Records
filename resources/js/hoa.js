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
// Modal Functions
// =========================================

/**
 * Opens the HOA modal for a specific record and loads the file list.
 * @param {Object} record - The record data.
 */
function openHoaModal(record) {
    const fieldConfig = {
        region: 'region',
        docket_no: 'docket-no',
        hoa_name: 'hoa-name',
        province: 'province',
        municipality: 'municipality',
        status: 'status',
        quantity: 'quantity',
        remarks: 'remarks'
    };

    // Handle nested province and municipality
    const recordTransformer = (rec) => ({
        ...rec,
        province: rec.province?.province_name ?? 'N/A',
        municipality: rec.municipality?.municipality_name ?? 'N/A'
    });

    openGenericModal(record, 'hoa', fieldConfig, recordTransformer);

    // Attach edit functionality event listeners after modal is opened
    setTimeout(() => {
        const editBtn = document.getElementById('hoa-edit-btn');
        const saveIcon = document.getElementById('hoa-save-icon');
        const cancelIcon = document.getElementById('hoa-cancel-icon');
        const editFileNameBtn = document.getElementById('hoa-edit-file-name-btn');

        if (editBtn) editBtn.addEventListener('click', () => {
            const editableFields = ['status', 'quantity', 'remarks'];
            const allFields = ['region', 'docket-no', 'hoa-name', 'province', 'municipality', 'status', 'quantity', 'remarks'];
            window.enterEditMode('hoa', editableFields, allFields);
        });
        if (saveIcon) saveIcon.addEventListener('click', () => {
            const buildFormData = () => ({
                region: document.getElementById('region').value,
                docket_no: document.getElementById('docket-no').value,
                hoa_name: document.getElementById('hoa-name').value,
                location: window.currentRecord.location, // Keep original location
                province_id: window.currentRecord.province_id,
                municipality_id: window.currentRecord.municipality_id,
                status: document.getElementById('status').value,
                quantity: document.getElementById('quantity').value || null,
                remarks: document.getElementById('remarks').value,
            });
            const allFields = ['region', 'docket-no', 'hoa-name', 'province', 'municipality', 'status', 'quantity', 'remarks'];
            window.saveEdit('hoa', buildFormData, allFields);
        });
        if (cancelIcon) cancelIcon.addEventListener('click', () => {
            const allFields = ['region', 'docket-no', 'hoa-name', 'province', 'municipality', 'status', 'quantity', 'remarks'];
            window.cancelEdit('hoa', allFields);
        });
        if (editFileNameBtn) editFileNameBtn.addEventListener('click', () => window.enterFileNameEditMode('hoa'));
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
     * Filters the table rows based on search input and filter selections.
     */
    const filterTable = () => {
        const query = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value.toLowerCase();
        const selectedProvince = provinceFilter.value.toLowerCase();
        const selectedMunicipality = municipalityFilter.value.toLowerCase();
        let anyVisible = false;
        let visibleCount = 0;
        const totalRows = getTableRows().length;

        getTableRows().forEach(row => {
            const data = row.dataset;
            const matchesSearch = Object.values(data).some(val => val.toLowerCase().includes(query));
            const matchesStatus = !selectedStatus || data.status === selectedStatus;
            const matchesProvince = !selectedProvince || data.province.toLowerCase() === selectedProvince;
            const matchesMunicipality = !selectedMunicipality || data.municipality.toLowerCase() === selectedMunicipality;
            const matchesRegion = !activeRegionFilter || data.region.toLowerCase() === activeRegionFilter;

            const shouldShow = matchesSearch && matchesStatus && matchesProvince && matchesMunicipality && matchesRegion;
            row.style.display = shouldShow ? '' : 'none';
            if (shouldShow) {
                anyVisible = true;
                visibleCount++;
            }
        });

        // Update region filter status
        if (activeRegionFilter) {
            regionFilterStatus.style.display = 'block';
            filteredCountSpan.textContent = visibleCount;
            totalCountSpan.textContent = totalRows;
            activeRegionSpan.textContent = activeRegionFilter;
        } else {
            regionFilterStatus.style.display = 'none';
        }

        const noRecordsRow = document.getElementById('noRecordsRow');
        if (noRecordsRow) {
            if (anyVisible) {
                noRecordsRow.classList.add('hidden');
            } else {
                noRecordsRow.classList.remove('hidden');
            }
        }
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
            filterTable();
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
            // Validate form before opening confirmation modal
            const fields = [
                { id: 'add-docket-no', name: 'Docket No' },
                { id: 'add-hoa-name', name: 'HOA Name' },
                { id: 'add-location', name: 'Location' },
                { id: 'add-province', name: 'Province' },
                { id: 'add-municipality', name: 'Municipality' },
                { id: 'add-status', name: 'Status' },
                { id: 'add-quantity', name: 'Quantity', min: 1 }
            ];
            if (!window.validateForm(fields)) return;
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'confirm-save-record-modal' } }));
        });
    }

    // Reset form when opening add record modal
    window.addEventListener('open-modal', (e) => {
        if (e.detail.name === 'add-record') {
            addRecordForm.reset();
            municipalitySelect.disabled = true;
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

                if (response.ok) {
                    // Reset form after successful submission
                    form.reset();
                    const municipalitySelect = document.getElementById('add-municipality');
                    if (municipalitySelect) municipalitySelect.disabled = true;

                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-save-record-modal' } }));
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-record' } }));
                    window.showToast('HOA record added successfully!', 'success');
                    await updateHoaData();
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
    provinceSelect.addEventListener('change', async () => {
        const provinceId = provinceSelect.value;
        municipalitySelect.innerHTML = '<option value="">Select Municipality</option>';
        municipalitySelect.disabled = !provinceId;

        if (provinceId) {
            try {
                const response = await fetch(`/hoa/municipalities?province_id=${provinceId}`);
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
 */
async function updateHoaData() {
    window.updateData('hoa');
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
    ['region', 'docket_no', 'hoa_name', 'location', 'province', 'municipality', 'remarks', 'status'].forEach(col => {
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
        <td class="px-6 py-4 text-center text-sm text-gray-900">${record.region || '-'}</td>
        <td class="px-6 py-4 text-center text-sm text-gray-900">${record.docket_no}</td>
        <td class="px-6 py-4 text-center text-sm text-gray-900">${record.hoa_name}</td>
        <td class="px-6 py-4 text-center text-sm text-gray-900">${record.location}</td>
        <td class="px-6 py-4 text-center text-sm text-gray-900">${record.province?.province_name || 'N/A'}</td>
        <td class="px-6 py-4 text-center text-sm text-gray-900">${record.municipality?.municipality_name || 'N/A'}</td>
        <td class="px-6 py-4 text-center text-sm">
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



// =========================================
// Initialization
// =========================================

document.addEventListener('DOMContentLoaded', () => {
    initHoaRecords();
    initAddRecordModal();
});


