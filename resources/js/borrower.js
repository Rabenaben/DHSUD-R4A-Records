document.addEventListener('DOMContentLoaded', initBorrowerRecords);

function initBorrowerRecords() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('tbody.bg-white.divide-y.divide-gray-200');
    const addRecordBtn = document.getElementById('add-record-btn');
    const borrowerForm = document.getElementById('borrower-form');
    const cancelBtn = document.getElementById('cancel-btn');
    const fileLocationSelect = document.getElementById('file-location');
    const docketInput = document.getElementById('docket-no');


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


    };

    // Generalized Filter Docket List
    const filterDocketList = (selectElement, docketInputElement, hoaListId, remListId) => {
        const selectedLocation = selectElement.value;
        if (selectedLocation === 'HOA Records') {
            docketInputElement.setAttribute('list', hoaListId);
        } else if (selectedLocation === 'REM Records') {
            docketInputElement.setAttribute('list', remListId);
        } else {
            docketInputElement.removeAttribute('list');
        }
    };

    // Open modal
    let openModal = (fromHistory = false, borrowerName = null) => {
        resetModalForAdding(fromHistory, borrowerName);
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'borrower' } }));
    };

    // Close modal
    const closeModal = () => {
        borrowerForm.reset();
        window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'borrower' } }));
    };

    // Generalized Handle Borrower Form Submission
    const handleBorrowerFormSubmit = async (e, formElement, isHistory = false) => {
        e.preventDefault();

        const formData = new FormData(formElement);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('/borrowers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error('HTTP error ' + response.status);
            }

            const result = await response.json();

            if (result.success) {
                if (isHistory) {
                    // Reset history form fields
                    formElement.reset();
                    // Reset file location dropdown to default
                    const fileLocationSelect = document.getElementById('history-file-location');
                    if (fileLocationSelect) {
                        fileLocationSelect.value = '';
                    }
                    // Reset docket list
                    const docketInput = document.getElementById('history-docket-no');
                    if (docketInput) {
                        docketInput.removeAttribute('list');
                    }

                    // Refresh history modal if it's open
                    const borrowerName = document.getElementById('history-borrower-name').value;
                    if (borrowerName) {
                        // Reset to page 1 to ensure new record is visible
                        currentPage = 1;
                        // Re-fetch and re-populate history
                        fetch(`/borrowers/history/${encodeURIComponent(borrowerName)}`, {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        }).then(response => response.json()).then(result => {
                            if (result.success) {
                                populateHistoryModal(result.borrower_name, result.history);
                            }
                        });
                    }
                } else {
                    // Close modal
                    closeModal();
                    // Reset form
                    formElement.reset();
                    // Open the record history modal for the created borrower
                    window.editBorrower(result.borrower.id);
                }

                // Add new record to main table
                if (result.borrower) {
                    addRecordToTable(result.borrower);
                }
                // Show success toast
                window.showToast(result.message, 'success');
            } else {
                window.showToast(result.message || 'Unknown error', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            window.showToast('Error saving record. Please try again.', 'error');
        }
    };

    searchInput.addEventListener('input', filterTable);

    if (fileLocationSelect) {
        fileLocationSelect.addEventListener('change', () => filterDocketList(fileLocationSelect, docketInput, 'hoa-docket-list', 'rem-docket-list'));
    }

    if (addRecordBtn) {
        addRecordBtn.addEventListener('click', () => openModal(false));
    }

    if (borrowerForm) {
        borrowerForm.addEventListener('submit', (e) => handleBorrowerFormSubmit(e, borrowerForm, false));
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

        // Check if a row for this borrower already exists
        const existingRow = document.querySelector(`tr[data-borrower-name="${borrower.borrower_name}"]`);

        if (existingRow) {
            // Update the existing row's status to 'Borrowed'
            const statusCell = existingRow.querySelector('td:nth-child(3)');
            if (statusCell) {
                statusCell.textContent = borrower.status || 'Borrowed';
            }
            existingRow.setAttribute('data-status', borrower.status || 'Borrowed');
        } else {
            // Hide no records row if it exists
            if (noRecordsRow) {
                noRecordsRow.classList.add('hidden');
            }

            // Create new row
            const newRow = document.createElement('tr');
            newRow.setAttribute('data-id', borrower.id);
            newRow.setAttribute('data-borrower-name', borrower.borrower_name);
            newRow.setAttribute('data-status', borrower.status || 'N/A');

            newRow.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">${borrower.id}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">${borrower.borrower_name}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">${borrower.status || 'Borrowed'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                    <button class="text-blue-600 hover:text-blue-900" onclick="editBorrower(${borrower.id})">Edit</button>
                </td>
            `;

            // Insert at the top of the table
            tableBody.insertBefore(newRow, tableBody.firstChild);
        }
    };

    // Function to reset modal for adding new record
    const resetModalForAdding = (fromHistory = false, borrowerName = null) => {
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

        // Set date borrowed to current date
        const dateLoanedField = document.getElementById('date-loaned');
        if (dateLoanedField) {
            dateLoanedField.value = new Date().toISOString().split('T')[0];
        }

        // Make fields editable
        const inputs = document.querySelectorAll('#borrower-form input, #borrower-form select, #borrower-form textarea');
        inputs.forEach(input => {
            if (input.id !== 'borrower-id') { // Keep ID readonly
                input.removeAttribute('readonly');
                input.classList.remove('bg-gray-100');
            }
        });

        // If from history, set borrower name to the provided borrower name and make it readonly
        if (fromHistory && borrowerName) {
            const borrowerNameField = document.getElementById('borrower-name');
            if (borrowerNameField) {
                borrowerNameField.value = borrowerName;
                borrowerNameField.setAttribute('readonly', 'readonly');
                borrowerNameField.classList.add('bg-gray-100');
            }
        }

        // Show save button, change cancel to Cancel
        const saveBtn = document.getElementById('save-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        if (saveBtn) saveBtn.style.display = 'inline-block';
        if (cancelBtn) cancelBtn.textContent = 'Cancel';
    };



    // Function to edit borrower (now shows history)
    window.editBorrower = async (id) => {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (!row) {
            window.showToast('Borrower record not found.', 'error');
            return;
        }

        const borrowerName = row.getAttribute('data-borrower-name');

        try {
            const response = await fetch(`/borrowers/history/${encodeURIComponent(borrowerName)}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (result.success) {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'borrower-record-history' } }));
                setTimeout(() => populateHistoryModal(result.borrower_name, result.history), 100);
            } else {
                window.showToast('Error loading borrower history: ' + (result.message || 'Unknown error'), 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            window.showToast('Error loading borrower history. Please try again.', 'error');
        }
    };

    // Global variables for pagination
    let currentPage = 1;
    const itemsPerPage = 3;
    let currentHistory = [];

    // Function to populate history modal
    const populateHistoryModal = (borrowerName, history) => {
        // Set borrower name display
        document.getElementById('borrower-name-display').textContent = borrowerName;

        // Sort history by date_borrowed descending (most recent first)
        currentHistory = history.sort((a, b) => new Date(b.date_borrowed) - new Date(a.date_borrowed));
        currentPage = 1;

        // Populate history table with pagination
        renderHistoryTable();

        // Set borrower name in form
        document.getElementById('history-borrower-name').value = borrowerName;
        // Set borrower ID (using the first record's ID as representative)
        document.getElementById('history-borrower-id').value = history.length > 0 ? history[0].id : '';

        // Add event listener for add new record button
        const addNewRecordBtn = document.getElementById('add-new-record-btn');
        if (addNewRecordBtn) {
            addNewRecordBtn.addEventListener('click', () => {
                closeHistoryModal();
                openModal(true, borrowerName); // Pass borrowerName from history
            });
        }
    };

    // Function to render history table with current page
    const renderHistoryTable = () => {
        const tableBody = document.getElementById('borrower-history-table');
        const paginationContainer = document.getElementById('pagination-container');
        const pageInfo = document.getElementById('page-info');

        tableBody.innerHTML = '';

        if (currentHistory.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No borrowing history found.</td></tr>';
            paginationContainer.style.display = 'none';
            return;
        }

        const totalPages = Math.ceil(currentHistory.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const pageHistory = currentHistory.slice(startIndex, endIndex);

        pageHistory.forEach(record => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${record.docket_number}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${record.file_location}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${new Date(record.date_borrowed).toLocaleString()}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 ${record.date_returned ? '' : 'cursor-pointer text-blue-600 hover:text-blue-800'}" id="returned-date-${record.id}" ${record.date_returned ? '' : `onclick="window.openVerifyReturnedDateModal(${record.id})"`}>${record.date_returned ? new Date(record.date_returned).toLocaleString() : 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${record.status === 'Returned' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                        ${record.status}
                    </span>
                </td>
            `;
            tableBody.appendChild(row);
        });

        // Update pagination
        if (totalPages > 1) {
            paginationContainer.style.display = 'flex';
            pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
            document.getElementById('prev-page').disabled = currentPage === 1;
            document.getElementById('next-page').disabled = currentPage === totalPages;
        } else {
            paginationContainer.style.display = 'none';
        }
    };

    // Global variable to store current record ID for modal
    let currentRecordId = null;

    // Function to open verify returned date modal
    window.openVerifyReturnedDateModal = (id) => {
        currentRecordId = id;
        // Find the record data
        const record = currentHistory.find(r => r.id === id);
        if (!record) return;

        // Populate borrower name
        const borrowerNameInput = document.getElementById('verify-borrower-name');
        if (borrowerNameInput) {
            borrowerNameInput.value = document.getElementById('history-borrower-name').value;
        }
        // Populate borrower ID
        const borrowerIdInput = document.getElementById('verify-borrower-id');
        if (borrowerIdInput) {
            borrowerIdInput.value = document.getElementById('history-borrower-id').value;
        }
        // Populate docket number
        const docketNoInput = document.getElementById('verify-docket-no');
        if (docketNoInput) {
            docketNoInput.value = record.docket_number;
        }
        // Set current date as read-only
        const returnedDateInput = document.getElementById('verify-returned-date');
        if (returnedDateInput) {
            returnedDateInput.value = new Date().toISOString().split('T')[0];
        }
        // Show verify returned date modal
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'verify-returned-date-modal' } }));
    };



    // Function to handle verify returned date modal submission
    const handleVerifyReturnedDate = async () => {
        if (!currentRecordId) return;

        const returnedDateInput = document.getElementById('verify-returned-date');
        const newValue = returnedDateInput.value;
        if (!newValue) return;

        // Show confirmation modal
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'confirm-returned-date-modal' } }));

        // Set up confirmation button
        const confirmBtn = document.getElementById('confirm-returned-yes-btn');
        const handleConfirm = async () => {
            await updateReturnedDate(currentRecordId, newValue + 'T' + new Date().toTimeString().split(' ')[0]); // Add current time
            // Update the cell to show the new date and time
            const cell = document.getElementById(`returned-date-${currentRecordId}`);
            if (cell) {
                cell.innerHTML = new Date(newValue + 'T' + new Date().toTimeString().split(' ')[0]).toLocaleString();
                cell.classList.remove('cursor-pointer', 'text-blue-600', 'hover:text-blue-800');
            }
            // Mark as returned to prevent further editing
            const record = currentHistory.find(r => r.id === currentRecordId);
            if (record) {
                record.date_returned = newValue + 'T' + new Date().toTimeString().split(' ')[0];
                record.status = 'Returned';
                // Update the status cell in the table
                const statusCell = document.querySelector(`#returned-date-${currentRecordId}`).closest('tr').querySelector('td:last-child span');
                if (statusCell) {
                    statusCell.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800';
                    statusCell.textContent = 'Returned';
                }
            }
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'confirm-returned-date-modal' } }));
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'verify-returned-date-modal' } }));
            confirmBtn.removeEventListener('click', handleConfirm);
            currentRecordId = null;
        };
        confirmBtn.addEventListener('click', handleConfirm);
    };



    // Function to close history modal
    const closeHistoryModal = () => {
        window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'borrower-record-history' } }));
    };

    // Function to update returned date
    const updateReturnedDate = async (id, dateValue) => {
        try {
            const response = await fetch(`/borrowers/${id}/return`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ date_returned: dateValue })
            });

            if (!response.ok) {
                throw new Error('HTTP error ' + response.status);
            }

            const result = await response.json();

            if (result.success) {
                // Update the main borrower table status if provided
                if (result.borrower_status) {
                    const borrowerName = result.borrower.borrower_name;
                    const mainTableRow = document.querySelector(`tr[data-borrower-name="${borrowerName}"]`);
                    if (mainTableRow) {
                        mainTableRow.setAttribute('data-status', result.borrower_status);
                        const mainStatusCell = mainTableRow.querySelector('td:nth-child(3)');
                        if (mainStatusCell) {
                            mainStatusCell.textContent = result.borrower_status;
                        }
                    }
                }

                // Show success toast
                window.showToast('Returned date updated successfully.', 'success');
            } else {
                window.showToast('Error updating returned date: ' + (result.message || 'Unknown error'), 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            window.showToast('Error updating returned date. Please try again.', 'error');
        }
    };

    // Function to update table row
    const updateTableRow = (borrower) => {
        const row = document.querySelector(`tr[data-id="${borrower.id}"]`);
        if (row) {
            row.setAttribute('data-borrower-name', borrower.borrower_name);
            const nameCell = row.querySelector('td:nth-child(2)');
            if (nameCell) {
                nameCell.textContent = borrower.borrower_name;
            }
        }
    };



    // Pagination event listeners
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');

    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderHistoryTable();
            }
        });
    }

    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', () => {
            const totalPages = Math.ceil(currentHistory.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderHistoryTable();
            }
        });
    }

    // Add event listeners for history modal
    const historyForm = document.getElementById('borrower-history-form');
    const historyCancelBtn = document.getElementById('history-cancel-btn');
    const historyFileLocationSelect = document.getElementById('history-file-location');

    if (historyForm) {
        historyForm.addEventListener('submit', (e) => handleBorrowerFormSubmit(e, historyForm, true));
    }

    if (historyCancelBtn) {
        historyCancelBtn.addEventListener('click', closeHistoryModal);
    }

    if (historyFileLocationSelect) {
        historyFileLocationSelect.addEventListener('change', () => filterDocketList(historyFileLocationSelect, document.getElementById('history-docket-no'), 'history-hoa-docket-list', 'history-rem-docket-list'));
    }

    // Add event listener for verify returned date modal
    const verifyReturnedDateBtn = document.getElementById('verify-returned-date-btn');
    if (verifyReturnedDateBtn) {
        verifyReturnedDateBtn.addEventListener('click', handleVerifyReturnedDate);
    }
}
