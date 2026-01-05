# TODO: Implement Redirection to pages/home.php after Successful Login or Register

## Steps to Complete

- [x] Update functions/register.php to set session variables after successful user registration
- [x] Update js/index.js to redirect to 'pages/home.php' after successful login or register response
- [ ] Test the changes to ensure redirection works correctly

## Notes
- Ensure session is started in register.php (already is).
- After registration success, set $_SESSION['user_id'] and $_SESSION['username'].
- In JavaScript, replace alert with window.location.href = 'pages/home.php';
