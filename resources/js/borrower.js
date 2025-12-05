document.addEventListener('DOMContentLoaded', initBorrowerRecords);

function initBorrowerRecords() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('tbody.bg-white.divide-y.divide-gray-200');
    const addRecordBtn = document.getElementById('add-record-btn');
    const borrowerForm = document.getElementById('borrower-form');
    const cancelBtn = document.getElementById('cancel-btn');

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

    // Open modal
    const openModal = () => {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'borrower' } }));
    };

    // Close modal
    const closeModal = () => {
        window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'borrower' } }));
    };

    // Handle form submission
    const handleFormSubmit = async (e) => {
        e.preventDefault();

        const formData = new FormData(borrowerForm);
        const data = Object.fromEntries(formData.entries());

        // Convert status_id to integer
        if (data.status_id) {
            data.status_id = parseInt(data.status_id);
        }

        try {
            const response = await fetch('/borrowers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                // Close modal
                closeModal();
                // Reset form
                borrowerForm.reset();
                // Add new record to table without reloading
                if (result.borrower) {
                    addRecordToTable(result.borrower);
                }
            } else {
                alert('Error saving record: ' + (result.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error saving record. Please try again.');
        }
    };

    searchInput.addEventListener('input', filterTable);

    if (addRecordBtn) {
        addRecordBtn.addEventListener('click', openModal);
    }

    if (borrowerForm) {
        borrowerForm.addEventListener('submit', handleFormSubmit);
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }

    // X button close functionality
    const closeBtn = document.querySelector('button[onclick*="closeModal(\'borrower\'"]');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    // Function to add new record to table
    const addRecordToTable = (borrower) => {
        const tableBody = document.querySelector('tbody.bg-white.divide-y.divide-gray-200');
        const noRecordsRow = document.getElementById('noRecordsRow');

        if (!tableBody) return;

        // Hide no records row if it exists
        if (noRecordsRow) {
            noRecordsRow.classList.add('hidden');
        }

        // Create new row
        const newRow = document.createElement('tr');
        newRow.setAttribute('data-id', borrower.id);
        newRow.setAttribute('data-borrower-name', borrower.borrower_name);
        newRow.setAttribute('data-status', borrower.recordStatus ? borrower.recordStatus.status_name : 'N/A');
        newRow.setAttribute('data-remarks', borrower.remarks || '');
        newRow.setAttribute('onclick', `showBorrowerDetails(${borrower.id})`);
        newRow.style.cursor = 'pointer';

        newRow.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${borrower.id}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${borrower.borrower_name}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${borrower.recordStatus ? borrower.recordStatus.status_name : 'N/A'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${borrower.remarks || ''}</td>
        `;

        // Insert at the top of the table
        tableBody.insertBefore(newRow, tableBody.firstChild);
    };

    // Function to show borrower details in modal
    window.showBorrowerDetails = async (id) => {
        try {
            const response = await fetch(`/borrowers/${id}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (result.success) {
                populateModalForViewing(result.borrower);
                window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'borrower' } }));
            } else {
                alert('Error loading borrower details: ' + (result.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error loading borrower details. Please try again.');
        }
    };

    // Function to populate modal for viewing details
    const populateModalForViewing = (borrower) => {
        // Change modal title
        const modalTitle = document.getElementById('modal-title');
        if (modalTitle) {
            modalTitle.textContent = 'Borrower Details';
        }

        // Populate form fields
        document.getElementById('borrower-id').value = borrower.id || '';
        document.getElementById('docket-no').value = borrower.docket_number || '';
        document.getElementById('file-name').value = borrower.file_name || '';
        document.getElementById('file-location').value = borrower.file_location || '';
        document.getElementById('borrower-name').value = borrower.borrower_name || '';
        document.getElementById('date-loaned').value = borrower.date_borrowed ? new Date(borrower.date_borrowed).toISOString().slice(0, 16) : '';
        document.getElementById('date-returned').value = borrower.date_returned ? new Date(borrower.date_returned).toISOString().slice(0, 16) : '';
        document.getElementById('status').value = borrower.status_id || '';
        document.getElementById('remarks').value = borrower.remarks || '';

        // Make all fields readonly
        const inputs = document.querySelectorAll('#borrower-form input, #borrower-form select, #borrower-form textarea');
        inputs.forEach(input => {
            input.setAttribute('readonly', 'readonly');
            input.classList.add('bg-gray-100');
        });

        // Hide save button, show close button
        const saveBtn = document.getElementById('save-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        if (saveBtn) saveBtn.style.display = 'none';
        if (cancelBtn) cancelBtn.textContent = 'Close';
    };

    // Function to reset modal for adding new record
    const resetModalForAdding = () => {
        // Change modal title back
        const modalTitle = document.getElementById('modal-title');
        if (modalTitle) {
            modalTitle.textContent = 'Add New Borrower Record';
        }

        // Clear form
        borrowerForm.reset();

        // Set automatic ID
        const idField = document.getElementById('borrower-id');
        if (idField && window.nextId) {
            idField.value = window.nextId;
        }

        // Make fields editable
        const inputs = document.querySelectorAll('#borrower-form input, #borrower-form select, #borrower-form textarea');
        inputs.forEach(input => {
            if (input.id !== 'borrower-id') { // Keep ID readonly
                input.removeAttribute('readonly');
                input.classList.remove('bg-gray-100');
            }
        });

        // Show save button, change cancel to Cancel
        const saveBtn = document.getElementById('save-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        if (saveBtn) saveBtn.style.display = 'inline-block';
        if (cancelBtn) cancelBtn.textContent = 'Cancel';
    };

    // Override open modal to reset for adding
    const originalOpenModal = openModal;
    openModal = () => {
        resetModalForAdding();
        originalOpenModal();
    };
}
