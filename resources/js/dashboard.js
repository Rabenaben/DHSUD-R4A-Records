// Borrowed status card functionality for dashboard
document.addEventListener('DOMContentLoaded', function() {
    const borrowedCard = document.querySelector('.borrowed-card');
    const closeBtn = document.getElementById('closeBorrowedModal');
    const tableBody = document.getElementById('borrowedRecordsTableBody');

    if (!borrowedCard || !closeBtn || !tableBody) return;

    // Click handler for borrowed card
    borrowedCard.addEventListener('click', async function() {
        tableBody.innerHTML = '<tr><td colspan="3" class="px-6 py-12 text-center text-gray-500 text-lg">Loading borrowed records...</td></tr>';

        try {
            const response = await fetch('/dashboard/borrowed-records');
            const records = await response.json();

            if (records.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="3" class="px-6 py-12 text-center text-gray-500 text-lg">No borrowed records found.</td></tr>';
            } else {
                tableBody.innerHTML = records.map(record => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 text-center text-sm font-medium text-gray-900">${record.docket_no}</td>
                        <td class="px-4 py-4 text-center text-sm text-gray-900">${record.record_name}</td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">${record.type}</span>
                        </td>
                    </tr>
                `).join('');
            }

            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'borrowed-records' } }));
        } catch (error) {
            console.error('Error loading borrowed records:', error);
            tableBody.innerHTML = '<tr><td colspan="3" class="px-6 py-12 text-center text-red-500 text-lg">Error loading records. Please try again.</td></tr>';
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'borrowed-records' } }));
        }
    });

    // Close modal handlers
    function closeModal() {
        window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'borrowed-records' } }));
    }

    closeBtn.addEventListener('click', closeModal);

    // Close on overlay click
    // `x-modal` handles backdrop click automatically.

    // x-modal handles Escape key automatically by closing itself.
});
