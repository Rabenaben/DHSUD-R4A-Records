<<<<<<< HEAD
# TODO: Add Timestamps to REM Table

- [x] Edit the migration file `database/migrations/2025_11_14_031003_create_hoa-rem_table.php` to add `$table->timestamps()` to the rem table schema.
- [x] Update the `RemDatabase` model in `app/Models/RemDatabase.php` to enable timestamps by setting `$timestamps = true`.
- [x] Provide the SQL code for adding timestamps to the rem table.
- [ ] Run `php artisan migrate` to apply the changes if the migration hasn't been run yet.
=======
# Borrower Record History Updates

## Tasks
- [x] Update borrower-record-history-modal.blade.php: Remove Date Returned and Status fields from Add New Borrowing Record form, add confirmation modal
- [x] Update borrower.js: Modify handleHistoryFormSubmit to default status to 'Borrowed', add confirmation modal logic to makeReturnedDateEditable, prevent re-editing returned date once set
>>>>>>> 9474b923b1863520f3ebaea1194bc7d7ec468cc3
