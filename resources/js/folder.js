export function initFolderClicks() {
    const folderContainer = document.getElementById('folderContainer');
    if (!folderContainer) return;

    // Save original folder section HTML
    const originalFolderHTML = folderContainer.innerHTML;

    document.querySelectorAll('.folder').forEach(folder => {
        folder.addEventListener('click', () => loadFolderContent(folder, folderContainer, originalFolderHTML));
    });
}

async function loadFolderContent(folder, container, originalFolderHTML) {
    const { theme, province } = folder.dataset;

    showLoading(container);

    try {
        const response = await fetch(`/${theme}/folder/${province}`);
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
    const searchInput = container.querySelector('#searchInput');
    const statusFilter = container.querySelector('#statusFilter');
    const tableBody = container.querySelector('#folderTableBody');
    const noRecordsRow = container.querySelector('#noRecordsRow');

    if (!tableBody) return;

    const filterRows = () => {
        const searchValue = (searchInput?.value || '').trim().toLowerCase();
        const statusValue = (statusFilter?.value || '').trim().toUpperCase();
        let visibleRows = 0;

        tableBody.querySelectorAll('.data-row').forEach(row => {
            const [docket, project, statusCell] = Array.from(row.cells).map(cell => cell.textContent.trim());
            const matchesSearch = [docket, project].some(text => text.toLowerCase().includes(searchValue));
            const matchesStatus = !statusValue || statusCell.toUpperCase().includes(statusValue);

            updateRowColor(row, statusCell);
            row.style.display = matchesSearch && matchesStatus ? '' : 'none';
            if (matchesSearch && matchesStatus) visibleRows++;
        });

        if (noRecordsRow) noRecordsRow.classList.toggle('hidden', visibleRows > 0);
    };

    // Attach listeners
    [searchInput, statusFilter].forEach(input => input?.addEventListener('input', filterRows));
    [statusFilter].forEach(input => input?.addEventListener('change', filterRows));

    // Run once on load
    filterRows();
}

function updateRowColor(row, status) {
    const colors = {
        'ON-SHELF': 'bg-green-100',
        'BORROWED': 'bg-yellow-100',
        'UNAVAILABLE': 'bg-red-100'
    };

    row.classList.remove(...Object.values(colors));
    for (const [key, color] of Object.entries(colors)) {
        if (status.toUpperCase().includes(key)) {
            row.classList.add(color);
            break;
        }
    }
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
