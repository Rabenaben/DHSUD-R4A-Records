# TODO - Archive Table Styling Updates

## Task: Center data in table header and table data in archived files, allow text breaking, and set column widths

### Steps:
- [x] 1. Analyze the archive.blade.php file
- [x] 2. Center-align table headers (th elements)
- [x] 3. Center-align table data cells (td elements)
- [x] 4. Remove whitespace-nowrap and add break-words for text wrapping
- [x] 5. Set equal width to columns 1,2,3,5,6,7
- [x] 6. Make File Name column wider than others
- [x] 7. Verify JavaScript doesn't need changes (confirmed - it only handles search/modal)

## Completed Changes:
- Changed `text-left` to `text-center` in all `<th>` and `<td>` elements
- Replaced `whitespace-nowrap` with `break-words` in all `<td>` elements
- Added column widths: Type (w-20), Docket No (w-32), Record Name (w-40), File Name (w-auto), Date Added (w-36), Last Updated By (w-36), Action (w-24)
- File Name column is now wider (w-auto) than the other columns
