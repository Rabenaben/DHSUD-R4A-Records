# TODO: Implement Modal for Archived Files

## Steps to Complete
- [x] Create resources/views/archived/partials/archive-modal.blade.php (modeled after hoa-modal and rem-modal, with read-only fields for basic info and file preview section)
- [x] Update resources/views/archived/archive.blade.php to include the new archive-modal component
- [x] Update resources/js/archive.js to add click event listener on '.archive-row' elements: open modal, populate fields, load file preview
- [ ] Test modal opening on row click
- [ ] Verify data population and file preview functionality
- [ ] Ensure no conflicts with existing unarchive buttons
