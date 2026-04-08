# Overdue Notices Optimization Task

## Current Status
- [x] Analyzed implementation (backend DB query)
- [x] Plan approved by user

## Steps to Complete

### 1. Database Optimization
- [x] Create migration for overdue index
- [x] Run `php artisan migrate`

### 2. Caching Implementation
- [x] Add Cache::remember to `getOverdueNotices()`
- [x] Add Cache::forget in `storeBorrower()` & `updateReturnedDate()`

### 3. Testing
- [ ] Test `/overdue-notices` endpoint (fresh vs cached)
- [ ] Verify invalidation on borrow/return
- [ ] Check query performance

### 4. Completion
- [ ] Update TODO.md as done
- [ ] attempt_completion
