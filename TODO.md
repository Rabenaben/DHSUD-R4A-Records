# Role Access Control for Staff Accounts

## Tasks to Complete

- [x] Hide edit, archive, and add file buttons in hoa-modal.blade.php for staff
- [x] Hide edit, archive, and add file buttons in rem-modal.blade.php for staff
- [x] Hide add docket button in search-filter-bar.blade.php for staff
- [x] Hide add docket button in folder-table.blade.php for staff
- [x] Hide unarchive button in archive.blade.php for staff
- [x] Hide pencil icon (edit file name) in hoa-modal.blade.php for staff
- [x] Hide pencil icon (edit file name) in rem-modal.blade.php for staff
- [x] Hide add record button in borrowers.blade.php for staff
- [x] Hide Action column and edit buttons in borrowers.blade.php for staff

## Implementation Details

Use `@unless(auth()->user()->role === 'Staff')` to conditionally show buttons only for non-staff users.
