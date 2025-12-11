<<<<<<< HEAD
# TODO: Remove Status Input from Borrower Modal and Default to "Borrowed"

## Tasks
- [x] Remove the Status select field from borrower-modal.blade.php (lines 47-55)
- [x] Update DisplayController.php storeBorrower method: remove 'status' from validation rules and add 'status' => 'Borrowed' to Borrower::create
- [x] Update borrower.js handleFormSubmit function: add data.status = 'Borrowed' before sending AJAX request
- [ ] Test the borrower form submission to ensure status defaults to "Borrowed"
- [ ] Verify viewing borrower details still works (status populated readonly)
=======
- [ ] Add file input field to add-file-modal.blade.php for PDF uploads
- [ ] Verify the modal form handles file uploads correctly
>>>>>>> 045868f51307859c3d69f1c59120ac8e0106a3c7
