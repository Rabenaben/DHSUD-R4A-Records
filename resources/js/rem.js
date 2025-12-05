// Modal functions for REM records

// openFileListModal is the same as in hoa.js
function openFileListModal(record) {
    const files = [
        { name: 'REM_Document_001.pdf', dateModified: '2023-10-01' },
        { name: 'REM_Document_002.pdf', dateModified: '2023-09-15' },
        { name: 'REM_Document_003.pdf', dateModified: '2023-08-20' }
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
        openRemModal(record, fileName);

        tbody.removeEventListener('click', onFileClick); // remove listener to avoid duplicates
    });

    // Open modal
    window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'file-list' } }));
}

function openRemModal(record, fileName) {
    // Store the record for back navigation
    window.currentRecord = record;

    const setValue = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.value = value ?? '';
    };

    setValue('rem-docket-no', record.docket_no);
    setValue('rem-project-name', record.project_name);
    setValue('rem-status', record.status);
    setValue('rem-quantity', record.quantity);
    setValue('rem-remarks', record.remarks ?? '');

    const fileLabel = document.getElementById('rem-file-label');
    if (fileLabel) fileLabel.textContent = fileName ?? '';

    window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'rem' } }));
}

// Make functions global
window.openFileListModal = openFileListModal;
window.openRemModal = openRemModal;

// Make remGoBackToFileList global
window.remGoBackToFileList = function () {
    // Close rem modal and reopen file-list modal with stored record
    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'rem' } }));
    if (window.currentRecord) {
        openFileListModal(window.currentRecord);
    }
};

// Folder functions moved from folder.js
function initFolderClicks() {
    const folderContainer = document.getElementById('folderContainer');
    if (!folderContainer) return;

    // Save original folder section HTML
    const originalFolderHTML = folderContainer.innerHTML;

    document.querySelectorAll('.folder').forEach(folder => {
        folder.addEventListener('click', () => loadFolderContent(folder, folderContainer, originalFolderHTML));
    });
}

async function loadFolderContent(folder, container, originalFolderHTML) {
    const province = folder.dataset.province;

    showLoading(container);

    try {
        const response = await fetch(`/rem/folder/${province}`);
        if (!response.ok) throw new Error('Failed to load folder content');

        const html = await response.text();
        container.innerHTML = html; // replaces entire folder section

        attachFilters(container);
        attachBackButton(container, originalFolderHTML);
    } catch (error) {
        console.error(error);
        container.innerHTML = `
            <div class="text-center py-6 text-red-600">
                Failed to load content. Please try again later.
            </div>`;
    }
}

function showLoading(container) {
    container.innerHTML = `
        <div class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            <span class="ml-2 text-gray-600">Loading...</span>
        </div>
    `;
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

        // Toggle the empty row
        noRecordsRow.style.display = visibleRows > 0 ? 'none' : '';
    };

    [searchInput, statusFilter].forEach(input => input?.addEventListener('input', filterRows));
    [statusFilter].forEach(input => input?.addEventListener('change', filterRows));

    // Delegate click event for REM rows
    tableBody.addEventListener('click', (e) => {
        const row = e.target.closest('tr.data-row');
        if (!row) return;

        const record = JSON.parse(row.dataset.record);
        openFileListModal(record);
    });

    filterRows(); // Run once on load
}

function attachBackButton(container, originalHTML) {
    const backBtn = container.querySelector('#backToFolders');
    if (!backBtn) return;

    backBtn.addEventListener('click', () => {
        container.innerHTML = originalHTML; // restore original folder section
        initFolderClicks(); // reattach click events
    });
}

document.addEventListener('DOMContentLoaded', initFolderClicks);
