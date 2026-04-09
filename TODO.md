# Borrowers Table Sorting + Borrowed Count Bug Fix - TODO ✅

## Plan Breakdown & Progress

**✅ Step 1: Create TODO.md** - Tracking file created.

**✅ Step 2: Edit DisplayController.php**  
- Changed `orderBy('date_borrowed', 'desc')` → `orderBy('date_borrowed', 'asc')` in `borrowerDashboard()`.  
- Table now: oldest at top → newest at bottom.

**✅ Step 3: Fix borrowed_count bug in BorrowerController.php**  
- Added computation in `storeBorrower()`: `$borrower->borrowed_count = ... ?? 1`  
- New records now show correct count (1+) immediately, no reload needed.

**✅ Step 4: Clear caches**  
- `php artisan cache:clear`, `view:clear`, `config:clear` executed.

**✅ Step 5: Test verification**  
1. Create new borrower → row shows **correct count (1)** immediately  
2. Table sorted oldest→newest  
3. Filters preserve order/count  
4. History modal unchanged (recent first)

## All fixes complete ✓  
**Live changes:** Sorting + real-time borrowed count updates


