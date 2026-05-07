- [x] Inspect AuthenticatedSessionController.php and routes/web.php for admin login/store issues
- [x] Fix AuthenticatedSessionController.php store() to call $request->authenticate() with no parameters
- [x] Ensure routes/web.php admin login POST route uses AuthenticatedSessionController@store (remove any incorrect Auth::login usage from route definition)
- [ ] Validate remaining “Call to member function any() on a non-object of type array” issue by checking the admin login blade for passing $errors as an array (if needed)
- [ ] Reproduce and confirm Page Expired/CSRF 419 fix by ensuring token regeneration and session configuration
- [ ] Run quick checks: php artisan route:list and attempt admin login to verify no 419

