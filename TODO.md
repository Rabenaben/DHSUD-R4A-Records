# TODO: Make Location Required for REM Records

## Task Summary
Require the location input for REM when adding a record and editing a record.

## Changes Required

### 1. Add Record Modal (`resources/views/components/add-record-modal.blade.php`)
- [x] Change REM Location field from `:required="false"` to `:required="true"`

### 2. Edit REM Modal (`resources/views/rem_records/partials/rem-modal.blade.php`)
- [x] Add required class/mark to the Location input label

### 3. JavaScript Validation (`resources/js/rem.js`)
- [x] Add 'rem-location' to the required fields in `validateRemFields` function

### 4. Server-side Validation (`app/Http/Controllers/RemController.php`)
- [x] Change location validation from `'nullable|string'` to `'required|string'` in both `store` and `update` methods
