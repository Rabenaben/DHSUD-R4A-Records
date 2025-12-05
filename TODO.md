# TODO: Add Search Bar to Borrowers Tab

## Steps to Complete

- [ ] Edit `resources/views/borrowers/borrower.blade.php`:
  - Add a simple search bar (input field) above the table, similar to the HOA search bar but without filters.
  - Add `data-*` attributes to each table row for ID, Borrower Name, Status, and Remarks to enable filtering.
  - Add a hidden "no records found" row in the table body that spans all columns and shows when no rows are visible.
  - Include `borrower.js` script tag at the end of the file.

- [ ] Create `resources/js/borrower.js`:
  - Implement client-side filtering logic: Listen for input on the search bar, filter table rows based on matching any of the data attributes (case-insensitive), hide/show rows accordingly.
  - Show/hide the "no records found" row based on whether any rows are visible.
  - Initialize on DOMContentLoaded.

- [ ] Test the implementation:
  - Verify search functionality works correctly.
  - Check "no records found" displays when no matches or no records exist.
