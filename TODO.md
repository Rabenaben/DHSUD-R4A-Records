# User Access Control Implementation

## Steps to Implement
- [x] Create RoleMiddleware to check user roles
- [x] Register the middleware in bootstrap/app.php
- [x] Apply middleware to routes that require Admin access (e.g., accounts routes)
- [x] Update navigation.blade.php to conditionally show Accounts link based on role
- [x] Update accounts/user.blade.php to conditionally show add/edit buttons for Admin only
- [x] Test access for Admin and Staff roles
