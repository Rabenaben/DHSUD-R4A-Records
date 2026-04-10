// =========================================
// Generic Export Utilities
// =========================================

/**
 * Generic export download handler - extracts duplicated fetch/blob/download logic
 * @param {string} exportType - 'hoa' or 'rem'
 * @param {string} endpoint - 'export', 'export-sql', or 'export-files'  
 * @param {string} filenameSuffix - 'xlsx', 'sql', or 'zip'
 * @param {string} successToast - Success message
 */
window.downloadExport = async function (exportType, endpoint, filenameSuffix, successToast) {
    const provinceSelect = document.getElementById(`export-${exportType}-province`);
    const municipalitySelect = document.getElementById(`export-${exportType}-municipality`);

    const provinceId = provinceSelect?.value || '';
    const municipalityId = municipalitySelect?.value || '';

    // Build URL with query parameters (exact existing logic)
    let url = `/${exportType}/${endpoint}?`;
    const params = new URLSearchParams();
    if (provinceId) params.append('province_id', provinceId);
    if (municipalityId) params.append('municipality_id', municipalityId);
    url += params.toString();

    // Show type-specific loading (existing)
    window.showExportLoading(exportType);

    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 30000);

    try {
        const response = await fetch(url, {
            signal: controller.signal,
            credentials: 'same-origin'
        });

        clearTimeout(timeoutId);

        // Files export JSON check (existing hoa.js logic)
        if (response.ok && response.headers.get('content-type')?.includes('application/json')) {
            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Export failed');
            }
        }

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const blob = await response.blob();
        let filename = `${exportType}_records${filenameSuffix === 'zip' ? '_files' : ''}.${filenameSuffix}`;

        // Content-Disposition filename extraction (existing)
        const contentDisposition = response.headers.get('Content-Disposition');
        if (contentDisposition) {
            const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
            if (filenameMatch && filenameMatch[1]) {
                filename = filenameMatch[1].replace(/['"]/g, '');
            }
        }

        // Download (existing exact logic)
        const downloadUrl = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = downloadUrl;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(downloadUrl);
        document.body.removeChild(a);

        window.showToast(successToast, 'success');
    } catch (error) {
        if (error.name === 'AbortError') {
            const timeoutMsg = filenameSuffix === 'zip' ? 'Files export timed out (30s). Large ZIPs may take longer.' : 'Export timed out (30s). Large datasets may take longer.';
            window.showToast(timeoutMsg, 'warning');
        } else {
            window.showToast('Export failed: ' + error.message, 'error');
        }
    } finally {
        window.hideExportLoading(exportType);
        if (timeoutId) clearTimeout(timeoutId);
        // Close modal after export (existing)
        window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: `export-${exportType}` } }));
    }
};

/**
 * Generic export initializer for hoa/rem modals
 * @param {string} exportType - 'hoa' or 'rem'
 */
window.initExport = function (exportType) {
    const exportBtn = document.getElementById(`export${exportType.charAt(0).toUpperCase() + exportType.slice(1)}Btn`);
    const cancelBtn = document.getElementById(`cancel-export-${exportType}-btn`);
    const provinceSelect = document.getElementById(`export-${exportType}-province`);
    const municipalitySelect = document.getElementById(`export-${exportType}-municipality`);

    if (!exportBtn) return;

    // Prevent duplicate listeners
    if (!exportBtn.dataset.exportListener) {
        exportBtn.dataset.exportListener = 'attached';
        exportBtn.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: `export-${exportType}` } }));
        });
    }

    // Cancel button
    if (cancelBtn && !cancelBtn.dataset.cancelListener) {
        cancelBtn.dataset.cancelListener = 'attached';
        cancelBtn.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: `export-${exportType}` } }));
        });
    }

    // Province → municipality cascading (existing exact logic)
    if (provinceSelect && !provinceSelect.dataset.provinceListener) {
        provinceSelect.dataset.provinceListener = 'attached';
        provinceSelect.addEventListener('change', async () => {
            const provinceId = provinceSelect.value;
            municipalitySelect.innerHTML = '<option value="">All Municipalities</option>';
            municipalitySelect.disabled = !provinceId;

            if (provinceId) {
                try {
                    const response = await fetch(`/${exportType}/municipalities?province_id=${provinceId}`);
                    const municipalities = await response.json();

                    municipalities.forEach(municipality => {
                        const option = document.createElement('option');
                        option.value = municipality.municipality_id;
                        option.textContent = municipality.municipality_name;
                        municipalitySelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Error fetching municipalities:', error);
                }
            }
        });
    }

    // 3 Export buttons: Excel, SQL, Files (exact btn IDs from files)
    const excelBtnId = `export-${exportType}-submit-btn`;
    const sqlBtnId = `export-${exportType}-sql-btn`;
    const filesBtnId = `export-${exportType}-files-btn`;

    // Excel - with debounce and listener check
    const excelBtn = document.getElementById(excelBtnId);
    if (excelBtn && !excelBtn.dataset.excelListener) {
        excelBtn.dataset.excelListener = 'attached';
        let debounceTimer;
        excelBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (debounceTimer) return;
            debounceTimer = setTimeout(() => {
                window.downloadExport(exportType, 'export', 'xlsx', `${exportType.toUpperCase()} Excel export completed!`);
                debounceTimer = null;
            }, 200);
        });
    }

    // SQL - with debounce and listener check
    const sqlBtn = document.getElementById(sqlBtnId);
    if (sqlBtn && !sqlBtn.dataset.sqlListener) {
        sqlBtn.dataset.sqlListener = 'attached';
        let debounceTimer;
        sqlBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (debounceTimer) return;
            debounceTimer = setTimeout(() => {
                window.downloadExport(exportType, 'export-sql', 'sql', `${exportType.toUpperCase()} SQL export completed!`);
                debounceTimer = null;
            }, 200);
        });
    }

    // Files (ZIP) - with debounce and listener check
    const filesBtn = document.getElementById(filesBtnId);
    if (filesBtn && !filesBtn.dataset.filesListener) {
        filesBtn.dataset.filesListener = 'attached';
        let debounceTimer;
        filesBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (debounceTimer) return;
            debounceTimer = setTimeout(() => {
                window.downloadExport(exportType, 'export-files', 'zip', `${exportType.toUpperCase()} files exported successfully!`);
                debounceTimer = null;
            }, 200);
        });
    }

    // Cleanup listeners on modal close
    const cleanupExportListeners = () => {
        const selectors = [
            `#export-${exportType}-submit-btn[data-excel-listener]`,
            `#export-${exportType}-sql-btn[data-sql-listener]`,
            `#export-${exportType}-files-btn[data-files-listener]`
        ];
        selectors.forEach(selector => {
            const btn = document.querySelector(selector);
            if (btn) {
                ['excelListener', 'sqlListener', 'filesListener'].forEach(type => {
                    if (btn.dataset[type]) {
                        // Note: can't remove named functions, but dataset prevents re-attachment
                        // console.log(`Export listener cleanup for ${type}`); // Removed
                    }
                });
            }
        });
    };

    window.addEventListener('close-modal', (e) => {
        if (e.detail.name === `export-${exportType}`) {
            cleanupExportListeners();
        }
    });
};
