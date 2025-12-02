document.addEventListener('DOMContentLoaded', initHoaRecords);

function initHoaRecords() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const tableBody = document.getElementById('hoaRecordsTable');
    const noBorrowedRow = document.getElementById('noBorrowedRow');

    if (!searchInput || !statusFilter || !tableBody) return;

    const getTableRows = () => Array.from(tableBody.querySelectorAll('tr.hoa-row'));

    // Filter Table
    const filterTable = () => {
        const query = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value.toLowerCase();
        let anyVisible = false;

        getTableRows().forEach(row => {
            const data = row.dataset;
            const matchesSearch = Object.values(data).some(val => val.toLowerCase().includes(query));
            const matchesStatus = !selectedStatus || data.status === selectedStatus;

            row.style.display = matchesSearch && matchesStatus ? '' : 'none';
            if (matchesSearch && matchesStatus) anyVisible = true;
        });

        if (noBorrowedRow) {
            noBorrowedRow.classList.toggle('hidden', anyVisible || selectedStatus !== 'borrowed');
        }
    };

    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);

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
}

// Make goBackToFileList global
window.goBackToFileList = function() {
    // Close hoa modal and reopen file-list modal with stored record
    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'hoa' } }));
    if (window.currentRecord) {
        openFileListModal(window.currentRecord);
    }
};
