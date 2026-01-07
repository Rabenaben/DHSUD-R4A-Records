# TODO: Prevent Borrowing Already Borrowed Dockets

- [ ] Modify BorrowerController.php storeBorrower() to check docket status before creating borrower record
- [ ] Update DisplayController.php borrowerDashboard() to filter out borrowed dockets from hoaDockets and remDockets
- [ ] Update borrower-modal.blade.php to use filtered docket lists
- [ ] Test the changes: attempt to borrow an already borrowed docket and verify error
- [ ] Verify modal only shows available dockets
- [ ] Check borrower dashboard updates correctly
