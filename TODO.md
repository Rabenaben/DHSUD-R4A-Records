`# TODO: Add "Export All Files" Button to HOA and REM Modals

## Task Summary
Add an "Export All Files" button beside the "Add File" button in both HOA and REM modals that will save all files associated with a specific record as a ZIP download.

## Plan

### 1. Information Gathered:
- **HOA Modal** (`resources/views/hoa_records/partials/hoa-modal.blade.php`): Has file list section with "Add File" button
- **REM Modal** (`resources/views/rem_records/partials/rem-modal.blade.php`): Similar structure with "Add File" button
- **File Handling** (`resources/js/file-utils.js`): Has `exportFile()` for single file export
- **Controllers**: Use `FileControllerTrait` for file operations (downloadFile, getFiles, etc.)

### 2. Implementation Plan:

#### Step 1: Add "Export All Files" button to HOA Modal
- Edit: `resources/views/hoa_records/partials/hoa-modal.blade.php`
- Add button beside "Add File" button in the file list header

#### Step 2: Add "Export All Files" button to REM Modal
- Edit: `resources/views/rem_records/partials/rem-modal.blade.php`
- Add button beside "Add File" button in the file list header

#### Step 3: Add exportAllFiles method to FileControllerTrait
- Edit: `app/Http/Controllers/FileControllerTrait.php`
- Add new method to download all files as ZIP

#### Step 4: Add JavaScript handler for Export All Files
- Edit: `resources/js/file-utils.js`
- Add `exportAllFiles()` function

#### Step 5: Add routes for export all endpoint
- Edit: `routes/web.php`
- Add routes for HOA and REM export all files

### 3. Dependent Files:
- `resources/views/hoa_records/partials/hoa-modal.blade.php`
- `resources/views/rem_records/partials/rem-modal.blade.php`
- `app/Http/Controllers/FileControllerTrait.php`
- `resources/js/file-utils.js`
- `routes/web.php`

### 4. Followup Steps:
- Test the export all functionality for both HOA and REM records
- Verify ZIP file is created and downloaded correctly
