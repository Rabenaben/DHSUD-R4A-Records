// Borrowed status card functionality for dashboard
document.addEventListener('DOMContentLoaded', function() {
    const borrowedCard = document.querySelector('.borrowed-card');
    const modal = document.getElementById('borrowedRecordsModal');
    const closeBtn = document.getElementById('closeBorrowedModal');
    const tableBody = document.getElementById('borrowedRecordsTableBody');

    if (!borrowedCard || !modal || !closeBtn || !tableBody) return;

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

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } catch (error) {
            console.error('Error loading borrowed records:', error);
            tableBody.innerHTML = '<tr><td colspan="3" class="px-6 py-12 text-center text-red-500 text-lg">Error loading records. Please try again.</td></tr>';
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    });

    // Close modal handlers
    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    closeBtn.addEventListener('click', closeModal);

    // Close on overlay click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
