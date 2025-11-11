export function initFolderClicks() {
    document.querySelectorAll('.folder').forEach(folder => {
        folder.addEventListener('click', async () => {
            const theme = folder.dataset.theme;
            const province = folder.dataset.province;
            const folderContainer = document.getElementById('folderContent');

            // Show loading indicator
            folderContainer.innerHTML = `
                <div class="flex justify-center items-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                    <span class="ml-2 text-gray-600">Loading...</span>
                </div>
            `;

            const response = await fetch(`/${theme}/folder/${province}`);
            const html = await response.text();
            folderContainer.innerHTML = html;

            // Re-attach search & filter functionality
            attachFilters(folderContainer);
        });
    });
}

function attachFilters(container) {
    const searchInput = container.querySelector('#searchInput');
    const statusFilter = container.querySelector('#statusFilter');
    const tableBody = container.querySelector('#folderTableBody');
    const noRecordsRow = container.querySelector('#noRecordsRow');

    if (!tableBody) return;

    [searchInput, statusFilter].forEach(input => {
        if (!input) return;
        input.addEventListener('input', filterRows);
        input.addEventListener('change', filterRows);
    });

    function filterRows() {
        const searchValue = searchInput?.value.toLowerCase() || '';
        const statusValue = statusFilter?.value.toLowerCase() || '';
        let visibleRows = 0;

        tableBody.querySelectorAll('.data-row').forEach(row => {
            const text = row.textContent.toLowerCase();
            const statusCell = row.cells[2].textContent.toLowerCase();

            const matchesSearch = text.includes(searchValue);
            const matchesStatus = statusValue === '' || statusCell === statusValue;

            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
            if (matchesSearch && matchesStatus) visibleRows++;
        });

        if (noRecordsRow) {
            noRecordsRow.classList.toggle('hidden', visibleRows > 0);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (typeof initFolderClicks === 'function') {
        initFolderClicks();
    }
});