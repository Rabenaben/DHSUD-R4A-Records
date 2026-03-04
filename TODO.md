# TODO: Add Key Stats to Request History Page

## Task Summary
Add a key stats section to the request history page showing the total number of requests for each specific document type, similar to the dashboard.

## Document Types to Track:
- Certificate of Incorporation
- Certificate of Amended By-Laws
- Certificate of Amended Articles of Incorporation
- Articles of Incorporation
- By-Laws
- Annual Report
- Election Report

## Implementation Steps:

### Step 1: Modify DisplayController
- [ ] Update `requestHistoryDashboard()` method to calculate stats for each document type
- [ ] Count occurrences of each document in all client requests' `requested_docs` field

### Step 2: Create Stats Component for Request History
- [ ] Create a new component or adapt existing status-cards for request history stats
- [ ] Display each document type with its count

### Step 3: Update Request History View
- [ ] Include the stats section in `request-history.blade.php`
- [ ] Pass stats data from controller to view

## Files to Edit:
1. `app/Http/Controllers/DisplayController.php` - Add stats calculation
2. `resources/views/request-history/request-history.blade.php` - Add stats display
3. Possibly create: `resources/views/components/request-stats.blade.php` (new component)
