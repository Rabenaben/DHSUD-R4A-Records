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
}

// =========================================
// Initialization
// =========================================

document.addEventListener('DOMContentLoaded', initHoaRecords);


