# TODO: Add Date and Time for Files in HOA Records

- [x] Update FileControllerTrait.php: Change validation rule for 'date_added' from 'date' to 'datetime'
- [x] Update add-file-modal.blade.php: Change input type from "date" to "datetime-local"
- [x] Update file-list-modal.blade.php: Change table header from "Date Modified" to "Date Added"
- [x] Update file-utils.js: Replace f.dateModified with f.date_added and format using new Date(f.date_added).toLocaleString()
