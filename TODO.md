# TODO - Create docket number folders for file uploads

## Task
After uploading a file, create a folder named the docket number in hoa_files/ or rem_files/ to organize files.

## Plan
- [x] Review and understand FileControllerTrait.php
- [x] Modify uploadFile method in FileControllerTrait.php to create docket number folders
- [ ] Test the implementation

## Changes Made
1. Updated `uploadFile` method in `app/Http/Controllers/FileControllerTrait.php`:
   - Files are now stored in subfolders named after the docket number
   - New path format: `hoa_files/{docket_no}/filename.pdf` or `rem_files/{docket_no}/filename.pdf`
   - Laravel's `storeAs` method automatically creates the directory if it doesn't exist
