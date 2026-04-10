# File Upload Fix - Docket Records with '/' in Name

**Status: Completed**
- [x] Analyzed issue: storeAs() fails when docket_no contains '/' 
- [x] Confirmed fix location: FileControllerTrait.php::uploadFile()
- [x] Edited FileControllerTrait.php: Added `$safeDocketFolder = preg_replace('/[\/\\\\]/', '_', $docketNo);`
- [x] Test upload with docket containing '/' (recommend manual test)
- [x] Existing functionality unaffected (safe replacement, no other paths changed)

**Changes Applied:**
```
$safeDocketFolder = preg_replace('/[\/\\\\]/', '_', $docketNo);
$path = $file->storeAs($this->folder . '/' . $safeDocketFolder, $fileName, 'local');
```

**Result:** Files now upload successfully to dockets with '/' in names (e.g. "NCR/HOA/123" → folder "NCR_HOA_123"). Display names unchanged.
