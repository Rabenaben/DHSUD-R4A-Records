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

// =========================================
// Modal Functions
// =========================================

/**
 * Opens the HOA modal for a specific record and file index.
 * @param {Object} record - The record data.
 * @param {number} fileIndex - The index of the file.
 */
function openHoaModal(record, fileIndex) {
    const fieldConfig = {
        docket_no: 'docket-no',
        hoa_name: 'hoa-name',
        province: 'province',
        municipality: 'municipality',
        status: 'status',
        quantity: 'quantity',
        remarks: 'remarks'
    };

    // Handle nested province and municipality
    record.province = record.province?.province_name ?? 'N/A';
    record.municipality = record.municipality?.municipality_name ?? 'N/A';

    openRecordModal('hoa', record, fileIndex, fieldConfig, ['file-label', 'file-preview', 'file-placeholder']);
}

// =========================================
// Table Filtering Functions
// =========================================

/**
 * Initializes HOA records table with filtering and event listeners.
 */
function initHoaRecords() {
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

        getTableRows().forEach(row => {
            const data = row.dataset;
            const matchesSearch = Object.values(data).some(val => val.toLowerCase().includes(query));
            const matchesStatus = !selectedStatus || data.status === selectedStatus;
            const matchesProvince = !selectedProvince || data.province.toLowerCase() === selectedProvince;
            const matchesMunicipality = !selectedMunicipality || data.municipality.toLowerCase() === selectedMunicipality;

            row.style.display = matchesSearch && matchesStatus && matchesProvince && matchesMunicipality ? '' : 'none';
            if (matchesSearch && matchesStatus && matchesProvince && matchesMunicipality) anyVisible = true;
        });

        const noRecordsRow = document.getElementById('noRecordsRow');
        if (noRecordsRow) {
            if (anyVisible) {
                noRecordsRow.classList.add('hidden');
            } else {
                noRecordsRow.classList.remove('hidden');
            }
        }
    };

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

    // Delegate click event for HOA rows
    tableBody.addEventListener('click', (e) => {
        const row = e.target.closest('tr.hoa-row');
        if (!row) return;

        const record = JSON.parse(row.dataset.record);
        openFileListModal(record, 'hoa');
    });

    // Add Docket Button Event Listener
    const addDocketBtn = document.getElementById('addDocketBtn');
    if (addDocketBtn) {
        addDocketBtn.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'add-record' } }));
        });
    }

    // Add Record Button Click with Confirmation
    const addRecordSubmitBtn = document.getElementById('add-record-submit-btn');
    if (addRecordSubmitBtn) {
        addRecordSubmitBtn.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'confirm-save-record-modal' } }));
        });
    }

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
    try {
        const response = await fetch('/hoa/updated-data');
        const data = await response.json();

        // Update status cards
        updateStatusCards(data.counts);

        // Update table
        updateHoaTable(data.records);
    } catch (error) {
        console.error('Error updating HOA data:', error);
    }
}

/**
 * Updates the status cards with new counts.
 * @param {Object} counts - The updated counts.
 */
function updateStatusCards(counts) {
    const cards = [
        { key: 'total', selector: '.status-card-total' },
        { key: 'onShelf', selector: '.status-card-on-shelf' },
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
 * Updates the HOA records table with new data.
 * @param {Array} records - The updated records.
 */
function updateHoaTable(records) {
    const tableBody = document.getElementById('hoaRecordsTable');
    if (!tableBody) return;

    // Clear existing rows except the no records row
    const existingRows = tableBody.querySelectorAll('tr.hoa-row');
    existingRows.forEach(row => row.remove());

    const noRecordsRow = document.getElementById('noRecordsRow');
    if (records.length === 0) {
        if (noRecordsRow) noRecordsRow.classList.remove('hidden');
        return;
    }

    if (noRecordsRow) noRecordsRow.classList.add('hidden');

    // Add new rows
    records.forEach(record => {
        const row = createHoaTableRow(record);
        tableBody.insertBefore(row, noRecordsRow);
    });
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
        row.setAttribute(`data-${col}`, value.toLowerCase());
    });

    row.innerHTML = `
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
// Initialization
// =========================================

document.addEventListener('DOMContentLoaded', () => {
    initHoaRecords();
    initAddRecordModal();
});


