# Municipality Filter for REM Folder Table

## Current Status
✅ **Plan Approved** - Ready to implement municipality filter after clicking province folder

## Analysis Summary
- ✅ folder-table.blade.php: Municipality column exists (index 3), filter structure ready
- ✅ RemController.php: `folder($provinceId)` eager-loads municipality relationship
- ✅ rem.js: `attachFilters()` scans cell text - municipality filter will work identically to status filter

## Detailed Implementation Plan

**1. resources/views/rem_records/partials/folder-table.blade.php** ⬜
```
Add municipality filter select after status filter:
<select id="remMunicipalityFilter" class="...">
  <option value="">All Municipalities</option>
  @foreach(unique municipalities from $records as $mun)
    <option value="{{ $mun->municipality_name }}">{{ $mun->municipality_name }}</option>
  @endforeach
</select>
```

**2. resources/js/rem.js** ⬜  
```
In attachFilters():
- Add municipalityValue = municipalityFilter?.value.trim().toUpperCase()
- Filter condition: matchesMunicipality = !municipalityValue || cells[3].toUpperCase() === municipalityValue
- Add event listeners: municipalityFilter.on('input/change', filterRows)
```

## Execution Steps
- [⬜] **Step 1:** Update folder-table.blade.php (municipality dropdown)
- [⬜] **Step 2:** Update rem.js (filter logic)  
- [⬜] **Step 3:** Test: Click folder → Verify dropdown → Test filtering
- [✅] **Step 0:** TODO.md created

## Dependencies
- None (pure frontend, municipality data already loaded)

## Testing Checklist
- [⬜] Municipality dropdown populates with unique values
- [⬜] Filter hides/shows correct rows
- [⬜] Combines with search + status filters
- [⬜] Works after clicking any province folder
