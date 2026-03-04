// =========================================
// Request History Page JavaScript
// =========================================

/**
 * Initializes the Request History page functionality.
 */
function initRequestHistory() {
    // Get elements
    const addClientRequestBtn = document.getElementById('addClientRequestBtn');
    const cancelAddClientRequestBtn = document.getElementById('cancel-add-client-request-btn');
    const addClientRequestSubmitBtn = document.getElementById('add-client-request-submit-btn');
    const addClientRequestForm = document.getElementById('add-client-request-form');
    const searchInput = document.getElementById('requestSearchInput');
    const typeFilter = document.getElementById('typeFilter');

    // Open Add Client Request Form Modal and set today's date
    if (addClientRequestBtn) {
        addClientRequestBtn.addEventListener('click', () => {
            // Set today's date
            const dateInput = document.getElementById('request-date');
            if (dateInput) {
                const today = new Date().toISOString().split('T')[0];
                dateInput.value = today;
            }
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'add-client-request' } }));
        });
    }

    // Set today's date when modal is opened
    window.addEventListener('open-modal', (e) => {
        if (e.detail.name === 'add-client-request') {
            const dateInput = document.getElementById('request-date');
            if (dateInput) {
                const today = new Date().toISOString().split('T')[0];
                dateInput.value = today;
            }
        }
    });

    // Close modal on Cancel button click
    if (cancelAddClientRequestBtn) {
        cancelAddClientRequestBtn.addEventListener('click', () => {
            if (addClientRequestForm) {
                addClientRequestForm.reset();
            }
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-client-request' } }));
        });
    }

    // Submit form
    if (addClientRequestSubmitBtn) {
        addClientRequestSubmitBtn.addEventListener('click', async () => {
            // Validate required fields
            const requiredFields = [
                { id: 'request-date', name: 'Date' },
                { id: 'request-type', name: 'Type' },
                { id: 'project-name', name: 'Name of Project / HOA' },
                { id: 'docket-no', name: 'Docket No.' },
                { id: 'requested-by', name: 'Requested By' },
                { id: 'or-no', name: 'OR No.' },
                { id: 'amount', name: 'Amount' }
            ];

            let isValid = true;
            for (const field of requiredFields) {
                const element = document.getElementById(field.id);
                if (!element || !element.value.trim()) {
                    window.showToast(`${field.name} is required.`, 'error');
                    element?.focus();
                    isValid = false;
                    break;
                }
            }

            if (!isValid) return;

            // Collect form data
            const formData = new FormData(addClientRequestForm);
            const data = Object.fromEntries(formData.entries());

            // Get checked documents
            const checkedDocs = [];
            document.querySelectorAll('input[name="requested_docs[]"]:checked').forEach(checkbox => {
                checkedDocs.push(checkbox.value);
            });
            data.requested_docs = checkedDocs;

            try {
                const response = await fetch('/client-requests', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    const result = await response.json();

                    // Reset form and close modal
                    addClientRequestForm.reset();
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'add-client-request' } }));

                    window.showToast('Client request added successfully!', 'success');

                    // Fetch updated data and refresh table asynchronously
                    await refreshRequestHistoryTable();
                } else {
                    const errorData = await response.json();
                    window.showToast(errorData.message || 'Error adding request', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                window.showToast('Error adding request. Please try again.', 'error');
            }
        });
    }

    // Search input handler
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            filterRequestHistory();
        });
    }

    // Type filter handler
    if (typeFilter) {
        typeFilter.addEventListener('change', () => {
            filterRequestHistory();
        });
    }
}

/**
 * Filters the request history based on search input and type filter.
 */
function filterRequestHistory() {
    const searchInput = document.getElementById('requestSearchInput');
    const typeFilter = document.getElementById('typeFilter');
    const tableBody = document.querySelector('#request-history-table tbody');

    if (!tableBody) return;

    const searchTerm = searchInput?.value.toLowerCase() || '';
    const typeValue = typeFilter?.value || '';
    const rows = tableBody.querySelectorAll('tr');

    rows.forEach(row => {
        const projectName = row.dataset.projectName || '';
        const docketNo = row.dataset.docketNo || '';
        const clientName = row.dataset.clientName || '';
        const recordType = row.dataset.type || '';

        const matchesSearch = projectName.includes(searchTerm) ||
                              docketNo.includes(searchTerm) ||
                              clientName.includes(searchTerm);
        const matchesType = !typeValue || recordType === typeValue;

        if (matchesSearch && matchesType) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    // Update no results message
    const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
    const noResultsMessage = document.getElementById('no-results-message');
    if (noResultsMessage) {
        noResultsMessage.style.display = visibleRows.length === 0 ? 'block' : 'none';
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    initRequestHistory();
});

/**
 * Refreshes the request history table by fetching data from the server.
 */
async function refreshRequestHistoryTable() {
    try {
        const response = await fetch('/client-requests/data', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            const clientRequests = data.clientRequests || [];
            updateRequestHistoryTable(clientRequests);
        } else {
            console.error('Failed to fetch request history data');
        }
    } catch (error) {
        console.error('Error fetching request history:', error);
    }
}

/**
 * Updates the request history table with new data.
 */
function updateRequestHistoryTable(clientRequests) {
    const tableBody = document.querySelector('#request-history-table tbody');
    const noResultsMessage = document.getElementById('no-results-message');
    const tableContainer = document.querySelector('#request-history-table')?.closest('div.rounded-lg');

    if (!tableBody) return;

    // Clear existing rows
    tableBody.innerHTML = '';

    if (clientRequests.length === 0) {
        // Hide table, show no results message
        if (tableContainer) {
            const table = tableContainer.querySelector('table');
            if (table) table.style.display = 'none';
        }
        if (noResultsMessage) noResultsMessage.style.display = 'block';
        return;
    }

    // Show table, hide no results message
    if (tableContainer) {
        const table = tableContainer.querySelector('table');
        if (table) table.style.display = '';
    }
    if (noResultsMessage) noResultsMessage.style.display = 'none';

    // Add new rows
    clientRequests.forEach(request => {
        const row = document.createElement('tr');
        row.setAttribute('data-project-name', request.project_name.toLowerCase());
        row.setAttribute('data-docket-no', request.docket_no.toLowerCase());
        row.setAttribute('data-client-name', request.requested_by.toLowerCase());
        row.setAttribute('data-type', request.type);

        const typeClass = request.type === 'HOA'
            ? 'bg-blue-100 text-blue-800'
            : 'bg-green-100 text-green-800';

        // Format date
        const date = new Date(request.date);
        const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${formattedDate}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${request.docket_no}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${request.project_name}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${request.requested_by}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${typeClass}">
                    ${request.type}
                </span>
            </td>
        `;

        tableBody.appendChild(row);
    });
}

// Make refreshRequestHistoryTable available globally for external calls
