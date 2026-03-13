# Task: Add current province display beside "Back to Folders" button

## Plan Steps
- [x] 1. Edit resources/views/rem_records/partials/folder-table.blade.php: Add span#currentProvinceDisplay after button.
- [x] 2. Edit resources/js/rem.js: 
  - Store window.currentProvinceName = folder.dataset.provinceName in loadFolderContent.
  - After container.innerHTML = ..., update display: `Current Province: ${window.currentProvinceName}`.
  - In attachBackButton click: clear display.textContent = ''.
- [ ] 3. Test: Navigate to REM, click province folder, verify display shows "Current Province: [NAME]", back clears it.

Status: Code changes complete. Ready for testing.
