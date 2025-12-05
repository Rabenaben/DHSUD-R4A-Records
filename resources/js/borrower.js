document.addEventListener('DOMContentLoaded', initBorrowerRecords);

function initBorrowerRecords() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('tbody.bg-white.divide-y.divide-gray-200');

    if (!searchInput || !tableBody) return;

    const getTableRows = () => Array.from(tableBody.querySelectorAll('tr[data-id]'));

    // Filter Table
    const filterTable = () => {
        const query = searchInput.value.toLowerCase();
        let anyVisible = false;

        getTableRows().forEach(row => {
            const data = row.dataset;
            const matchesSearch = Object.values(data).some(val => val.toLowerCase().includes(query));

            row.style.display = matchesSearch ? '' : 'none';
            if (matchesSearch) anyVisible = true;
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
}
