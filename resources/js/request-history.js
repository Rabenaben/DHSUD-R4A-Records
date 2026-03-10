// =========================================
// Request History Page JavaScript
// =========================================

// Store client requests data globally for quick access
let clientRequestsData = [];

// Store current request data for edit mode
let currentRequestData = null;

// Store docket numbers data for quick access
let docketNumbersData = [];

// Consolidated form field IDs for validation and toggling
const FORM_FIELD_IDS = [
    'request-date',
    'request-type',
    'docket-no',
    'project-name',
    'location',
    'requested-by',
    'or-no',
    'amount',
    'remarks'
];

// Document lists for each type
const HOA_DOCUMENTS = [
    'Certificate of Incorporation',
    'Certificate of Amended By-Laws',
    'Certificate of Amended Articles of Incorporation',
    'Articles of Incorporation',
    'By-Laws',
    'Annual Report',
    'Election Report',
    'Masterlist',
    'General Information Sheet',
    'Others'
];

const REM_DOCUMENTS = [
    'Certificate of Registration and License to Sell (CRLS)',
    'Notarized Fact Sheet / Sales Report',
    'Development Permit',
    'Verified Survey Returns (VSR)',
    'Subdivision Development Plan (SDP)',
    'Others'
];

/**
 * Renders document checkboxes based on the selected type
 * @param {string} type - 'HOA' or 'REM'
 */
function renderDocumentsByType(type) {
    const container = document.getElementById('requested-docs-container');
    const othersInputContainer = document.getElementById('others-input-container');
    const certifiedTrueCopySection = document.getElementById('certified-true-copy-section');

    if (!container) return;

    // Clear existing content
    container.innerHTML = '';

    // Get the appropriate document list
    let documents = [];
    if (type === 'HOA') {
        documents = HOA_DOCUMENTS;
    } else if (type === 'REM') {
        documents = REM_DOCUMENTS;
    }

    if (documents.length === 0) {
        // No type selected, show message
        container.innerHTML = '<p class="text-sm text-gray-500" id="select-type-message">Please select a Type to see available documents.</p>';
        if (othersInputContainer) othersInputContainer.classList.add('hidden');
        // Hide Certified True Copy section when no type is selected
        if (certifiedTrueCopySection) certifiedTrueCopySection.classList.add('hidden');
        return;
    }

    // Show Certified True Copy section when a valid type is selected
    if (certifiedTrueCopySection) {
        certifiedTrueCopySection.classList.remove('hidden');
    }

    // Render document checkboxes
    documents.forEach(doc => {
        const label = document.createElement('label');
        label.className = 'flex items-center space-x-2';

        const input = document.createElement('input');
        input.type = 'checkbox';
        input.className = 'rounded border-gray-300 text-blue-600 focus:ring-blue-500';
        input.name = 'requested_docs[]';
        input.value = doc;

        // Add change event listener for "Others" checkbox
        if (doc === 'Others') {
            input.addEventListener('change', (e) => {
                if (othersInputContainer) {
                    othersInputContainer.classList.toggle('hidden', !e.target.checked);
                    if (!e.target.checked) {
                        const othersInput = document.getElementById('others-document-specify');
                        if (othersInput) othersInput.value = '';
                    }
                }
            });
        }

        const span = document.createElement('span');
        span.className = 'text-sm text-gray-700';
        span.textContent = doc;

        label.appendChild(input);
        label.appendChild(span);
        container.appendChild(label);
    });

// Hide the others input initially
    if (othersInputContainer) othersInputContainer.classList.add('hidden');
}

/**
 * Fetches docket numbers from the server based on type
 * @param {string} type - 'HOA', 'REM', or 'all'
 */
async function fetchDocketNumbers(type = 'all') {
    try {
        const response = await fetch(`/client-requests/dockets?type=${type}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const dockets = await response.json();
            docketNumbersData = dockets;
            return dockets;
        } else {
            console.error('Failed to fetch docket numbers');
            return [];
        }
    } catch (error) {
        console.error('Error fetching docket numbers:', error);
        return [];
    }
}

/**
 * Populates the docket number dropdown based on the selected type
 * @param {string} type - 'HOA' or 'REM'
 */
async function populateDocketDropdown(type) {
    const docketSelect = document.getElementById('docket-no');
    if (!docketSelect) return;

    // Clear existing options except the first one
    docketSelect.innerHTML = '<option value="">Select Docket No.</option>';

    // Fetch docket numbers based on type
    const dockets = await fetchDocketNumbers(type);

    // Filter dockets by type if needed
    const filteredDockets = type === 'all' 
        ? dockets 
        : dockets.filter(d => d.type === type);

    // Add options to the dropdown
    filteredDockets.forEach(docket => {
        const option = document.createElement('option');
        option.value = docket.docket_no;
        option.textContent = docket.docket_no;
        option.dataset.projectName = docket.project_name || '';
        option.dataset.location = docket.location || '';
        option.dataset.type = docket.type || '';
        docketSelect.appendChild(option);
    });
}

/**
 * Handles the docket number dropdown change event
 * Auto-populates the project name and location fields
 */
function handleDocketChange() {
    const docketSelect = document.getElementById('docket-no');
    const projectNameInput = document.getElementById('project-name');
    const locationInput = document.getElementById('location');

    if (!docketSelect) return;

    docketSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (projectNameInput && selectedOption.dataset.projectName) {
            projectNameInput.value = selectedOption.dataset.projectName;
        }
        
        if (locationInput && selectedOption.dataset.location) {
            locationInput.value = selectedOption.dataset.location;
        }
    });
}

/**
 * Initializes the Request History page functionality.
 */
function initRequestHistory() {
    // Get elements
    const addClientRequestBtn = document.getElementById('addClientRequestBtn');
    const cancelClientRequestBtn = document.getElementById('cancel-client-request-btn');
    const clientRequestSubmitBtn = document.getElementById('client-request-submit-btn');
    const clientRequestForm = document.getElementById('client-request-form');
    const searchInput = document.getElementById('requestSearchInput');
    const typeFilter = document.getElementById('typeFilter');
    const requestTypeSelect = document.getElementById('request-type');

    // Open Add Client Request Form Modal and set today's date
    if (addClientRequestBtn) {
        addClientRequestBtn.addEventListener('click', () => {
            openClientRequestModal('add');
        });
    }

    // Set up modal when opened
    window.addEventListener('open-modal', (e) => {
        if (e.detail.name === 'client-request-modal') {
            const mode = document.getElementById('client-request-mode')?.value || 'add';
            if (mode === 'add') {
                setTodayDate();
            }
        }
    });

// Handle Type selection change to render appropriate documents and populate docket dropdown
    if (requestTypeSelect) {
        requestTypeSelect.addEventListener('change', async (e) => {
            const selectedType = e.target.value;
            renderDocumentsByType(selectedType);
            
            // Clear project name and location when type changes
            const projectNameInput = document.getElementById('project-name');
            const locationInput = document.getElementById('location');
            if (projectNameInput) projectNameInput.value = '';
            if (locationInput) locationInput.value = '';
            
            // Populate docket dropdown based on selected type
            if (selectedType === 'HOA' || selectedType === 'REM') {
                await populateDocketDropdown(selectedType);
            } else {
                // If no type selected, clear the dropdown
                const docketSelect = document.getElementById('docket-no');
                if (docketSelect) {
                    docketSelect.innerHTML = '<option value="">Select Docket No.</option>';
                }
            }
        });
    }

    // Set up docket change handler
    handleDocketChange();

    // Close modal on Cancel button click
    if (cancelClientRequestBtn) {
        cancelClientRequestBtn.addEventListener('click', () => {
            const mode = document.getElementById('client-request-mode')?.value;

            if (mode === 'edit') {
                // If in edit mode, switch back to view mode instead of closing modal
                const modeInput = document.getElementById('client-request-mode');
                if (modeInput) modeInput.value = 'view';

                const modalTitle = document.getElementById('client-request-modal-title');
                if (modalTitle) modalTitle.textContent = 'View Client Request';

                // Reset Others checkbox and hide the specify input when switching to view mode
                const othersCheckbox = document.querySelector('input[name="requested_docs[]"][value="Others"]');
                if (othersCheckbox) {
                    othersCheckbox.checked = false;
                }
                const othersInputContainer = document.getElementById('others-input-container');
                if (othersInputContainer) {
                    othersInputContainer.classList.add('hidden');
                }
                const othersSpecify = document.getElementById('others-document-specify');
                if (othersSpecify) {
                    othersSpecify.value = '';
                }

                toggleViewMode(true);
            } else {
                // If in add mode, close the modal
                resetClientRequestModal();
                window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'client-request-modal' } }));
            }
        });
    }

    // Submit form
    if (clientRequestSubmitBtn) {
        clientRequestSubmitBtn.addEventListener('click', async () => {
            // Clear previous error styles
            clearValidationStyles();

// Validate required fields and other validations
            const requiredFields = [
                { id: 'request-date', name: 'Date', type: 'date' },
                { id: 'request-type', name: 'Type', type: 'select', options: ['HOA', 'REM'] },
                { id: 'docket-no', name: 'Docket No.', type: 'select' },
                { id: 'project-name', name: 'Name of Project / HOA', type: 'text', maxLength: 255 },
                { id: 'location', name: 'Location', type: 'text', maxLength: 255, required: false },
                { id: 'requested-by', name: 'Requested By', type: 'text', maxLength: 100 },
                { id: 'or-no', name: 'OR No.', type: 'text', maxLength: 50 },
                { id: 'amount', name: 'Amount', type: 'number', min: 0.01 }
            ];

            let isValid = true;
            for (const field of requiredFields) {
                const element = document.getElementById(field.id);
                if (!element) continue;

                let error = null;

                // Check if empty
                if (field.type === 'select') {
                    if (!element.value || element.value === '') {
                        error = `${field.name} is required.`;
                    } else if (field.options && !field.options.includes(element.value)) {
                        error = `Please select a valid ${field.name}.`;
                    }
                } else if (field.type === 'number') {
                    if (!element.value || element.value === '') {
                        error = `${field.name} is required.`;
                    } else if (parseFloat(element.value) < field.min) {
                        error = `${field.name} must be at least ${field.min}.`;
                    }
                } else {
                    // Check if field is required
                    if (field.required !== false) {
                        if (!element.value || !element.value.trim()) {
                            error = `${field.name} is required.`;
                        }
                    }
                    // Check maxlength (only if field has value)
                    if (element.value && element.value.trim()) {
                        if (field.maxLength && element.value.length > field.maxLength) {
                            error = `${field.name} must not exceed ${field.maxLength} characters.`;
                        } else if (field.pattern && field.pattern instanceof RegExp) {
                            if (!field.pattern.test(element.value)) {
                                error = `${field.name} contains invalid characters. Only letters, numbers, dashes, and underscores are allowed.`;
                            }
                        }
                    }
                }

                if (error) {
                    // Show error styling
                    element.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                    window.showToast(error, 'error');
                    element.focus();
                    isValid = false;
                    break;
                }
            }

            if (!isValid) return;

            // Validate requested_docs (checkboxes) - required, minimum 1, maximum 10
            const checkedDocs = [];
            document.querySelectorAll('input[name="requested_docs[]"]:checked').forEach(checkbox => {
                checkedDocs.push(checkbox.value);
            });

            if (checkedDocs.length === 0) {
                window.showToast('Please select at least one document.', 'error');
                isValid = false;
            } else if (checkedDocs.length > 10) {
                window.showToast('You can select up to 10 documents only.', 'error');
                isValid = false;
            }

            // Validate Others specification if selected
            if (checkedDocs.includes('Others')) {
                const othersSpecify = document.getElementById('others-document-specify');
                if (!othersSpecify || !othersSpecify.value.trim()) {
                    window.showToast('Please specify the document for "Others".', 'error');
                    isValid = false;
                }
            }

            // Validate Certified True Copy option if the section is visible
            const certifiedSection = document.getElementById('certified-true-copy-section');
            if (certifiedSection && !certifiedSection.classList.contains('hidden')) {
                const certifiedTrueCopy = document.querySelector('input[name="certification_status"]:checked');
                if (!certifiedTrueCopy) {
                    window.showToast('Please select a certification option (Certified or Not Certified).', 'error');
                    isValid = false;
                }
            }

            if (!isValid) return;

            // Get mode and handle accordingly
            const mode = document.getElementById('client-request-mode')?.value || 'add';
            const requestId = document.getElementById('client-request-id')?.value;

            if (mode === 'edit' && requestId) {
                await updateClientRequest(requestId, clientRequestForm, checkedDocs);
            } else {
                await createClientRequest(clientRequestForm, checkedDocs);
            }
        });
    }

    // Close view mode button
    const closeViewBtn = document.getElementById('close-view-btn');
    if (closeViewBtn) {
        closeViewBtn.addEventListener('click', () => {
            resetClientRequestModal();
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'client-request-modal' } }));
        });
    }

    // Edit button in view mode
    const editRequestBtn = document.getElementById('edit-request-btn');
    if (editRequestBtn) {
        editRequestBtn.addEventListener('click', () => {
            switchToEditMode();
        });
    }

    // Search input handler - server-side filtering
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchClientRequests();
            }, 300);
        });
    }

    // Type filter handler - server-side filtering
    if (typeFilter) {
        typeFilter.addEventListener('change', () => {
            searchClientRequests();
        });
    }

    // Initialize table row click handlers
    initTableRowClickHandlers();
}

/**
 * Searches client requests server-side using the search endpoint.
 */
async function searchClientRequests() {
    const searchInput = document.getElementById('requestSearchInput');
    const typeFilter = document.getElementById('typeFilter');

    const searchTerm = searchInput?.value || '';
    const typeValue = typeFilter?.value || '';

    try {
        const queryParams = new URLSearchParams();
        if (searchTerm) queryParams.append('q', searchTerm);
        if (typeValue) queryParams.append('type', typeValue);

        const response = await fetch(`/client-requests/search?${queryParams.toString()}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const clientRequests = await response.json();
            clientRequestsData = clientRequests;
            updateRequestHistoryTable(clientRequests);
        } else {
            console.error('Failed to search client requests');
        }
    } catch (error) {
        console.error('Error searching client requests:', error);
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    initRequestHistory();

    // Fetch initial data for table row click functionality
    refreshRequestHistoryTable();
});

/**
 * Initializes click handlers for table rows to view details
 */
function initTableRowClickHandlers() {
    const tableBody = document.querySelector('#request-history-table tbody');
    if (!tableBody) return;

    tableBody.addEventListener('click', (e) => {
        // Find the closest row
        const row = e.target.closest('tr');
        if (!row) return;

        // Get the request data from the row
        const docketNo = row.dataset.docketNo;
        const clientName = row.dataset.clientName;

        // Find the request in our data
        const request = clientRequestsData.find(r =>
            r.docket_no.toLowerCase() === docketNo &&
            r.requested_by.toLowerCase() === clientName
        );

        if (request) {
            openClientRequestModal('view', request);
        }
    });
}

/**
 * Opens the client request modal in the specified mode
 * @param {string} mode - 'add', 'view', or 'edit'
 * @param {object} requestData - The request data for view/edit mode
 */
function openClientRequestModal(mode, requestData = null) {
    const modalTitle = document.getElementById('client-request-modal-title');
    const modeInput = document.getElementById('client-request-mode');
    const form = document.getElementById('client-request-form');

    // Always reset form fields before populating with new data
    resetFormFields();

if (mode === 'add') {
        // Reset form for add mode
        if (form) form.reset();

        if (modalTitle) modalTitle.textContent = 'Add Client Request Form';
        if (modeInput) modeInput.value = 'add';

        // Show form fields
        toggleViewMode(false);

        // Set button text to "Submit Request" for add mode
        const submitBtn = document.getElementById('client-request-submit-btn');
        if (submitBtn) submitBtn.textContent = 'Submit Request';

        // Set today's date
        setTodayDate();

        // Clear documents container for add mode
        const docsContainer = document.getElementById('requested-docs-container');
        if (docsContainer) {
            docsContainer.innerHTML = '<p class="text-sm text-gray-500" id="select-type-message">Please select a Type to see available documents.</p>';
        }
        const othersInputContainer = document.getElementById('others-input-container');
        if (othersInputContainer) othersInputContainer.classList.add('hidden');

        // Clear docket dropdown for add mode
        const docketSelect = document.getElementById('docket-no');
        if (docketSelect) {
            docketSelect.innerHTML = '<option value="">Select Docket No.</option>';
        }
    } else if (mode === 'view' && requestData) {
        // Populate form with request data
        populateFormWithData(requestData);

        if (modalTitle) modalTitle.textContent = 'View Client Request';
        if (modeInput) modeInput.value = 'view';

        // Show view mode
        toggleViewMode(true);

        // Populate view-specific info
        populateViewModeInfo(requestData);
    }

    // Open the modal
    window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'client-request-modal' } }));
}

/**
 * Resets the modal to add mode
 */
function resetClientRequestModal() {
    const form = document.getElementById('client-request-form');
    if (form) form.reset();
    resetFormFields();

    const modeInput = document.getElementById('client-request-mode');
    if (modeInput) modeInput.value = 'add';

    const modalTitle = document.getElementById('client-request-modal-title');
    if (modalTitle) modalTitle.textContent = 'Add Client Request Form';

    // Reset button text to "Submit Request" for add mode
    const submitBtn = document.getElementById('client-request-submit-btn');
    if (submitBtn) submitBtn.textContent = 'Submit Request';

    // Hide Certified True Copy section in add mode
    const certifiedTrueCopySection = document.getElementById('certified-true-copy-section');
    if (certifiedTrueCopySection) certifiedTrueCopySection.classList.add('hidden');

    toggleViewMode(false);
}

/**
 * Resets form fields to default state
 */
function resetFormFields() {
    // Clear hidden ID field
    const idField = document.getElementById('client-request-id');
    if (idField) idField.value = '';

    // Clear any error styles
    clearValidationStyles();
}

/**
 * Sets today's date in the date field
 */
function setTodayDate() {
    const dateInput = document.getElementById('request-date');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;
    }
}

/**
 * Helper function to get requested_docs array
 * Laravel cast handles JSON conversion automatically
 */
function parseRequestedDocs(data) {
    return data.requested_docs || [];
}

/**
 * Populates the form with request data
 * @param {object} data - The request data
 */
async function populateFormWithData(data) {
    // Store current request data for edit mode
    currentRequestData = data;

    // Set hidden fields
    const idField = document.getElementById('client-request-id');
    if (idField) idField.value = data.id || '';

// Set form fields
    const fields = {
        'request-date': data.date,
        'request-type': data.type,
        'project-name': data.project_name,
        'location': data.location || '',
        'requested-by': data.requested_by,
        'or-no': data.or_no,
        'amount': data.amount,
        'remarks': data.remarks || ''
    };

    for (const [fieldId, value] of Object.entries(fields)) {
        const element = document.getElementById(fieldId);
        if (element) {
            element.value = value || '';
        }
    }

    // Handle docket-no dropdown - need to populate and select
    const docketSelect = document.getElementById('docket-no');
    if (docketSelect && data.docket_no) {
        // Use data.type directly since the select element may not have the value set yet
        // in view mode (the select is disabled and populated after this function is called)
        const selectedType = data.type || 'all';
        
        // Populate dropdown and then select the value
        await populateDocketDropdown(selectedType);
        
        // Try to set the value - if it doesn't exist in the dropdown (e.g., deleted from database),
        // add it as a custom option
        if (docketSelect.value !== data.docket_no) {
            const customOption = document.createElement('option');
            customOption.value = data.docket_no;
            customOption.textContent = data.docket_no;
            customOption.dataset.projectName = data.project_name || '';
            customOption.dataset.location = data.location || '';
            customOption.dataset.type = data.type || '';
            docketSelect.appendChild(customOption);
        }
        
        docketSelect.value = data.docket_no;
    }

    // IMPORTANT: Hide the others input container BEFORE setting checkboxes
    // to prevent the change event listener from showing it briefly
    const othersInputContainer = document.getElementById('others-input-container');
    if (othersInputContainer) {
        othersInputContainer.classList.add('hidden');
    }

    // Set checkboxes for requested docs
    const requestedDocs = parseRequestedDocs(data);
    if (requestedDocs.length > 0) {
        document.querySelectorAll('input[name="requested_docs[]"]').forEach(checkbox => {
            checkbox.checked = requestedDocs.includes(checkbox.value);
        });
    }

    // Populate the "Others" specify field with stored value
    const othersSpecify = document.getElementById('others-document-specify');
    if (othersSpecify && data.others_specify) {
        othersSpecify.value = data.others_specify;
    }

    // Populate Certified True Copy radio buttons
    const certifiedTrueCopy = document.getElementById('certified-true-copy');
    const notCertified = document.getElementById('not-certified');

    if (data.certified_true_copy === true || data.certified_true_copy === 1 || data.certified_true_copy === '1' || data.certified_true_copy === 'certified') {
        if (certifiedTrueCopy) certifiedTrueCopy.checked = true;
        if (notCertified) notCertified.checked = false;
    } else if (data.certified_true_copy === false || data.certified_true_copy === 0 || data.certified_true_copy === '0' || data.certified_true_copy === 'not_certified') {
        if (certifiedTrueCopy) certifiedTrueCopy.checked = false;
        if (notCertified) notCertified.checked = true;
    } else {
        // Default to not certified if no value
        if (certifiedTrueCopy) certifiedTrueCopy.checked = false;
        if (notCertified) notCertified.checked = true;
    }

    // Always hide the "Others" specify input in view mode
    // It's controlled separately from toggleViewMode() so we need to ensure it's hidden
    if (othersInputContainer) {
        othersInputContainer.classList.add('hidden');
    }
}

/**
 * Populates view mode specific info
 * @param {object} data - The request data
 */
function populateViewModeInfo(data) {
    const createdAt = document.getElementById('created-at');
    const updatedAt = document.getElementById('updated-at');

    if (createdAt && data.created_at) {
        createdAt.textContent = new Date(data.created_at).toLocaleString();
    }
    if (updatedAt && data.updated_at) {
        updatedAt.textContent = new Date(data.updated_at).toLocaleString();
    }

    // Show requested docs as tags in view mode
    const requestedDocs = parseRequestedDocs(data);
    const docsList = document.getElementById('requested-docs-list');
    if (docsList && requestedDocs.length > 0) {
        docsList.innerHTML = requestedDocs.map(doc =>
            `<span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">${doc}</span>`
        ).join('');
    }

    // Show Certified/Not Certified badge in view mode
    const certifiedTrueCopyView = document.getElementById('certified-true-copy-view');
    const certificationBadge = document.getElementById('certification-badge');

    if (certifiedTrueCopyView && certificationBadge) {
        const isCertified = data.certified_true_copy === true || data.certified_true_copy === 1 || data.certified_true_copy === '1' || data.certified_true_copy === 'certified';
        const isNotCertified = data.certified_true_copy === false || data.certified_true_copy === 0 || data.certified_true_copy === '0' || data.certified_true_copy === 'not_certified';

        if (isCertified) {
            certificationBadge.textContent = 'Certified';
            certificationBadge.className = 'rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800';
            certifiedTrueCopyView.classList.remove('hidden');
        } else if (isNotCertified) {
            certificationBadge.textContent = 'Not Certified';
            certificationBadge.className = 'rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800';
            certifiedTrueCopyView.classList.remove('hidden');
        } else {
            // Default to not certified
            certificationBadge.textContent = 'Not Certified';
            certificationBadge.className = 'rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800';
            certifiedTrueCopyView.classList.remove('hidden');
        }
    }
}

/**
 * Toggles between view mode and form mode
 * @param {boolean} isViewMode - Whether to show view mode
 */
function toggleViewMode(isViewMode) {
    const formButtons = document.getElementById('form-buttons');
    const viewModeButtons = document.getElementById('view-mode-buttons');
    const viewModeInfo = document.getElementById('view-mode-info');
    const requestedDocsContainer = document.getElementById('requested-docs-container');
    const requestedDocsView = document.getElementById('requested-docs-view');

    // Toggle form fields read-only state using consolidated constant
    FORM_FIELD_IDS.forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element) {
            element.readOnly = isViewMode;
            if (isViewMode) {
                element.classList.add('bg-gray-100', 'cursor-not-allowed');
            } else {
                element.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        }
    });

// Toggle select read-only state for request-type
    const selectField = document.getElementById('request-type');
    if (selectField) {
        selectField.disabled = isViewMode;
        if (isViewMode) {
            selectField.classList.add('bg-gray-100', 'cursor-not-allowed');
        } else {
            selectField.classList.remove('bg-gray-100', 'cursor-not-allowed');
        }
    }

    // Toggle docket-no select read-only state
    const docketSelect = document.getElementById('docket-no');
    if (docketSelect) {
        docketSelect.disabled = isViewMode;
        if (isViewMode) {
            docketSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
        } else {
            docketSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
        }
    }

    // Toggle checkbox read-only state
    document.querySelectorAll('input[name="requested_docs[]"]').forEach(checkbox => {
        checkbox.disabled = isViewMode;
        if (isViewMode) {
            checkbox.parentElement.classList.add('cursor-not-allowed', 'opacity-50');
        } else {
            checkbox.parentElement.classList.remove('cursor-not-allowed', 'opacity-50');
        }
    });

    // Toggle Certified True Copy radio buttons read-only state
    const ctcRadioCertified = document.getElementById('certified-true-copy');
    const ctcRadioNotCertified = document.getElementById('not-certified');

    if (ctcRadioCertified) {
        ctcRadioCertified.disabled = isViewMode;
        if (isViewMode) {
            ctcRadioCertified.parentElement.classList.add('cursor-not-allowed', 'opacity-50');
        } else {
            ctcRadioCertified.parentElement.classList.remove('cursor-not-allowed', 'opacity-50');
        }
    }

    if (ctcRadioNotCertified) {
        ctcRadioNotCertified.disabled = isViewMode;
        if (isViewMode) {
            ctcRadioNotCertified.parentElement.classList.add('cursor-not-allowed', 'opacity-50');
        } else {
            ctcRadioNotCertified.parentElement.classList.remove('cursor-not-allowed', 'opacity-50');
        }
    }

    // Toggle button visibility
    if (formButtons) {
        formButtons.classList.toggle('hidden', isViewMode);
    }
    if (viewModeButtons) {
        viewModeButtons.classList.toggle('hidden', !isViewMode);
    }
    if (viewModeInfo) {
        viewModeInfo.classList.toggle('hidden', !isViewMode);
    }

    // Toggle requested docs display
    if (requestedDocsContainer) {
        requestedDocsContainer.classList.toggle('hidden', isViewMode);
    }
    if (requestedDocsView) {
        requestedDocsView.classList.toggle('hidden', !isViewMode);
    }

    // Toggle Certified True Copy section - show only when type is selected
    const certifiedTrueCopySection = document.getElementById('certified-true-copy-section');
    const certifiedTrueCopyView = document.getElementById('certified-true-copy-view');
    const requestTypeSelect = document.getElementById('request-type');
    const selectedType = requestTypeSelect?.value;

    // Show section only when a type (HOA or REM) is selected
    if (certifiedTrueCopySection) {
        if (selectedType === 'HOA' || selectedType === 'REM') {
            certifiedTrueCopySection.classList.remove('hidden');
        } else {
            certifiedTrueCopySection.classList.add('hidden');
        }
    }

    // In view mode: hide radio buttons, show badge. In form mode: show radio buttons, hide badge
    const certificationSection = document.querySelector('#certified-true-copy-section .mt-2');
    if (certificationSection && certifiedTrueCopyView) {
        certificationSection.classList.toggle('hidden', isViewMode);
        certifiedTrueCopyView.classList.toggle('hidden', !isViewMode);
    }
}

/**
 * Switches from view mode to edit mode
 */
function switchToEditMode() {
    const modeInput = document.getElementById('client-request-mode');
    if (modeInput) modeInput.value = 'edit';

    const modalTitle = document.getElementById('client-request-modal-title');
    if (modalTitle) modalTitle.textContent = 'Edit Client Request';

    // Update button text to "Save" for edit mode
    const submitBtn = document.getElementById('client-request-submit-btn');
    if (submitBtn) submitBtn.textContent = 'Save';

    // Get the selected type and render the document checkboxes
    const typeSelect = document.getElementById('request-type');
    if (typeSelect && typeSelect.value) {
        // Render documents based on the selected type
        renderDocumentsByType(typeSelect.value);

        // Check the existing documents from currentRequestData
        if (currentRequestData && currentRequestData.requested_docs) {
            const requestedDocs = currentRequestData.requested_docs;

            // Check each document checkbox that matches the requested docs
            document.querySelectorAll('input[name="requested_docs[]"]').forEach(checkbox => {
                checkbox.checked = requestedDocs.includes(checkbox.value);
            });

            // Handle "Others" checkbox - show the input if "Others" was selected
            const othersCheckbox = document.querySelector('input[name="requested_docs[]"][value="Others"]');
            const othersInputContainer = document.getElementById('others-input-container');
            if (othersCheckbox && othersCheckbox.checked && othersInputContainer) {
                othersInputContainer.classList.remove('hidden');

                // Use the stored others_specify value from the database
                const othersSpecify = document.getElementById('others-document-specify');
                if (othersSpecify && currentRequestData && currentRequestData.others_specify) {
                    othersSpecify.value = currentRequestData.others_specify;
                }
            }
        }
    }

    toggleViewMode(false);
}

/**
 * Creates a new client request
 */
async function createClientRequest(form, checkedDocs) {
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    data.requested_docs = checkedDocs;

    // Include Others specification if selected
    const othersSpecify = document.getElementById('others-document-specify');
    if (othersSpecify && othersSpecify.value.trim()) {
        data.others_specify = othersSpecify.value.trim();
    }

    // Include Certified True Copy radio button value
    const certifiedTrueCopy = document.querySelector('input[name="certification_status"]:checked');
    // Default to "not_certified" if no selection
    data.certified_true_copy = certifiedTrueCopy ? certifiedTrueCopy.value : 'not_certified';

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
            resetClientRequestModal();
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'client-request-modal' } }));

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
}

/**
 * Updates an existing client request
 */
async function updateClientRequest(requestId, form, checkedDocs) {
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    data.requested_docs = checkedDocs;

    // Include Others specification if selected
    const othersSpecify = document.getElementById('others-document-specify');
    if (othersSpecify && othersSpecify.value.trim()) {
        data.others_specify = othersSpecify.value.trim();
    }

    // Include Certified True Copy radio button value
    const certifiedTrueCopy = document.querySelector('input[name="certification_status"]:checked');
    // Default to "not_certified" if no selection
    data.certified_true_copy = certifiedTrueCopy ? certifiedTrueCopy.value : 'not_certified';

    try {
        const response = await fetch(`/client-requests/${requestId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            const result = await response.json();

            // Reset form and close modal
            resetClientRequestModal();
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'client-request-modal' } }));

            window.showToast('Client request updated successfully!', 'success');

            // Fetch updated data and refresh table asynchronously
            await refreshRequestHistoryTable();
        } else {
            const errorData = await response.json();
            window.showToast(errorData.message || 'Error updating request', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        window.showToast('Error updating request. Please try again.', 'error');
    }
}

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
            const hoaStats = data.hoaStats || {};
            const remStats = data.remStats || {};

            // Store data globally for quick access
            clientRequestsData = clientRequests;

            updateRequestHistoryTable(clientRequests);
            updateChart(hoaStats, remStats);
        } else {
            console.error('Failed to fetch request history data');
        }
    } catch (error) {
        console.error('Error fetching request history:', error);
    }
}

/**
 * Updates the bar chart with new data.
 * @param {object} hoaStats - HOA document stats
 * @param {object} remStats - REM document stats
 */
function updateChart(hoaStats, remStats) {
    // Check if chart exists and update it
    if (typeof chartInstance !== 'undefined' && chartInstance) {
        // Get the current type from the button state
        const btnHoa = document.getElementById('btn-hoa');
        const btnRem = document.getElementById('btn-rem');
        const currentType = btnHoa && btnHoa.classList.contains('bg-blue-600') ? 'HOA' : 'REM';
        
        const docStats = currentType === 'HOA' ? hoaStats : remStats;
        const colors = currentType === 'HOA' 
            ? { background: 'rgba(59, 130, 246, 0.6)', border: 'rgba(59, 130, 246, 1)' }
            : { background: 'rgba(34, 197, 94, 0.6)', border: 'rgba(34, 197, 94, 1)' };

        // Short label mappings for graph display
        const shortLabels = {
            'Certificate of Incorporation': 'COI',
            'Certificate of Amended By-Laws': 'Cof Amended By-Laws',
            'Certificate of Amended Articles of Incorporation': 'Cof Amended AoI',
            'Articles of Incorporation': 'AoI',
            'By-Laws': 'By-Laws',
            'Annual Report': 'Annual Report',
            'Election Report': 'Election Report',
            'Masterlist': 'Masterlist',
            'General Information Sheet': 'GIS',
            'Certificate of Registration and License to Sell (CRLS)': 'CRLS',
            'Notarized Fact Sheet / Sales Report': 'Fact Sheet',
            'Development Permit': 'Dev Permit',
            'Verified Survey Returns (VSR)': 'VSR',
            'Subdivision Development Plan (SDP)': 'SDP'
        };

        const labels = Object.keys(docStats).map(key => shortLabels[key] || key);
        const chartData = Object.values(docStats);

        chartInstance.data.labels = labels;
        chartInstance.data.datasets[0].data = chartData;
        chartInstance.data.datasets[0].backgroundColor = colors.background;
        chartInstance.data.datasets[0].borderColor = colors.border;
        chartInstance.update();
    }
}

/**
 * Updates the stat cards with new data.
 */
function updateStatCards(docStats) {
    const statCardsContainer = document.getElementById('stat-cards-container');
    if (!statCardsContainer) return;

    const colors = {
        'Certificate of Incorporation': 'from-blue-500 to-blue-700',
        'Certificate of Amended By-Laws': 'from-indigo-500 to-indigo-700',
        'Certificate of Amended Articles of Incorporation': 'from-purple-500 to-purple-700',
        'Articles of Incorporation': 'from-pink-500 to-pink-700',
        'By-Laws': 'from-rose-500 to-rose-700',
        'Annual Report': 'from-amber-500 to-amber-700',
        'Election Report': 'from-teal-500 to-teal-700',
    };

    let html = '';
    for (const [docName, count] of Object.entries(docStats)) {
        const bgClass = colors[docName] || 'from-gray-500 to-gray-700';
        html += `
            <div class="relative flex h-20 items-center justify-between rounded-lg bg-white p-3 shadow transition-transform duration-200 hover:-translate-y-2 hover:transform">
                <div class="bg-linear-to-r ${bgClass} absolute bottom-0 left-0 top-0 w-2 rounded-l-lg"></div>
                <div class="flex flex-col pl-2">
                    <h2 class="text-lg font-bold leading-tight md:text-xl">
                        ${count}
                    </h2>
                    <p class="mt-1 text-xs font-semibold md:text-sm line-clamp-2" title="${docName}">
                        ${docName}
                    </p>
                </div>
            </div>
        `;
    }

    statCardsContainer.innerHTML = html;
}

/**
 * Updates the request history table with new data.
 */
function updateRequestHistoryTable(clientRequests) {
    const tableBody = document.querySelector('#request-history-table tbody');
    const noResultsMessage = document.getElementById('no-results-message');
    const noRecordsMessage = document.getElementById('no-records-message');
    const tableContainer = document.querySelector('#request-history-table')?.closest('div.rounded-lg');

    if (!tableBody) return;

    // Clear existing rows
    tableBody.innerHTML = '';

    if (clientRequests.length === 0) {
        // Hide table, show no records message
        if (tableContainer) {
            const table = tableContainer.querySelector('table');
            if (table) table.style.display = 'none';
        }
        if (noRecordsMessage) noRecordsMessage.style.display = 'block';
        return;
    }

    // Show table, hide no records message
    if (tableContainer) {
        const table = tableContainer.querySelector('table');
        if (table) table.style.display = '';
    }
    if (noRecordsMessage) noRecordsMessage.style.display = 'none';

    // Add new rows
    clientRequests.forEach(request => {
        const row = document.createElement('tr');
        row.setAttribute('data-project-name', request.project_name.toLowerCase());
        row.setAttribute('data-docket-no', request.docket_no.toLowerCase());
        row.setAttribute('data-client-name', request.requested_by.toLowerCase());
        row.setAttribute('data-type', request.type);

        // Add hover effect classes for consistency with Blade template
        row.classList.add('cursor-pointer', 'transition', 'hover:bg-gray-100');

        const typeClass = request.type === 'HOA'
            ? 'bg-blue-100 text-blue-800'
            : 'bg-green-100 text-green-800';

        // Format date - handle YYYY-MM-DD string to prevent timezone issues
        let formattedDate;
        if (request.date) {
            const dateObj = new Date(request.date + 'T00:00:00');
            formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        } else {
            formattedDate = '-';
        }

        row.innerHTML = `
            <td class="px-6 py-4 text-center text-sm text-gray-900">
                ${formattedDate}
            </td>
            <td class="px-6 py-4 text-center text-sm text-gray-900">
                ${request.docket_no}
            </td>
            <td class="px-6 py-4 text-center text-sm text-gray-900">
                ${request.project_name}
            </td>
            <td class="px-6 py-4 text-center text-sm text-gray-900">
                ${request.requested_by}
            </td>
            <td class="px-6 py-4 text-center">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${typeClass}">
                    ${request.type}
                </span>
            </td>
        `;

        tableBody.appendChild(row);
    });
}

/**
 * Clears validation error styles from form fields.
 */
function clearValidationStyles() {
    // Use consolidated FORM_FIELD_IDS constant
    FORM_FIELD_IDS.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        }
    });
}
