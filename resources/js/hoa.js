document.addEventListener('DOMContentLoaded', initHoaRecords);

function initHoaRecords() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const provinceFilter = document.getElementById('provinceFilter');
    const municipalityFilter = document.getElementById('municipalityFilter');
    const tableBody = document.getElementById('hoaRecordsTable');

    if (!searchInput || !statusFilter || !provinceFilter || !municipalityFilter || !tableBody) return;

    const getTableRows = () => Array.from(tableBody.querySelectorAll('tr.hoa-row'));

    // Filter Municipality options based on selected Province
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

    // Set Province when Municipality is selected
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

    // Filter Table
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
        openFileListModal(record);
    });
}

// Modal functions
function openFileListModal(record) {
    const files = [
        { name: 'HOA_Document_001.pdf', dateModified: '2023-10-01' },
        { name: 'HOA_Document_002.pdf', dateModified: '2023-09-15' },
        { name: 'HOA_Document_003.pdf', dateModified: '2023-08-20' }
    ];

    const tbody = document.getElementById('file-list-body');
    tbody.innerHTML = files.map(f => `
        <tr class="cursor-pointer hover:bg-gray-50 file-row" data-file-name="${f.name}">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${f.name}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${f.dateModified}</td>
        </tr>
    `).join('');

    // Delegate click for file rows
    tbody.addEventListener('click', function onFileClick(e) {
        const row = e.target.closest('tr.file-row');
        if (!row) return;

        const fileName = row.dataset.fileName;
        window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'file-list' } }));
        openHoaModal(record, fileName);

        tbody.removeEventListener('click', onFileClick); // remove listener to avoid duplicates
    });

    // Add File Button
    const addFileBtn = document.getElementById('add-file-btn');
    if (addFileBtn) {
        addFileBtn.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'file-list' } }));
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'add-file' } }));
        });
    }

    // Cancel Add File Button
    const cancelAddFileBtn = document.getElementById('cancel-add-file-btn');
    if (cancelAddFileBtn) {
        cancelAddFileBtn.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-file' } }));
        });
    }

    // Open modal
    window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'file-list' } }));
}

function openHoaModal(record, fileName) {
    // Store the record for back navigation
    window.currentRecord = record;

    const setValue = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.value = value ?? '';
    };

    setValue('docket-no', record.docket_no);
    setValue('hoa-name', record.hoa_name);
    setValue('province', record.province?.province_name ?? 'N/A');
    setValue('municipality', record.municipality?.municipality_name ?? 'N/A');
    setValue('status', record.status);
    setValue('quantity', record.quantity);
    setValue('remarks', record.remarks ?? '');

    const fileLabel = document.getElementById('file-label');
    if (fileLabel) fileLabel.textContent = fileName ?? '';

    window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'hoa' } }));

    // Attach archive button event
    const archiveBtn = document.getElementById('archive-hoa-btn');
    if (archiveBtn) {
        archiveBtn.addEventListener('click', () => archiveRecord('hoa', record.id));
    }
}

// Make goBackToFileList global
window.goBackToFileList = function () {
    // Close hoa modal and reopen file-list modal with stored record
    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'hoa' } }));
    if (window.currentRecord) {
        openFileListModal(window.currentRecord);
    }
};

// Archive record function
function archiveRecord(type, id) {
    if (!confirm('Are you sure you want to archive this record?')) return;

    fetch(`/${type}/${id}/archive`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Record archived successfully!');
        } else {
            alert('Failed to archive record.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while archiving the record.');
    });
}
