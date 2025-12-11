# Borrower Record History Updates

## Tasks
- [x] Update borrower-record-history-modal.blade.php: Remove Date Returned and Status fields from Add New Borrowing Record form, add confirmation modal
- [x] Update borrower.js: Modify handleHistoryFormSubmit to default status to 'Borrowed', add confirmation modal logic to makeReturnedDateEditable, prevent re-editing returned date once set
