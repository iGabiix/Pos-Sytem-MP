# Verification record

Verified with Windows, XAMPP PHP 8.2.12, MariaDB 10.4.32, and CodeIgniter 4.7.4.

- PHP and JavaScript syntax checks passed.
- The phpMyAdmin SQL import succeeded against a fresh feu_pos database.
- CodeIgniter's migration check passed after the import.
- Full PHPUnit suite: **16 tests, 34 assertions passed**.
- Sales service suite: **11 tests, 27 assertions passed** on SQLite and again on MariaDB.
- **19 HTTP workflow checks passed** against the application.
- Desktop login, dashboard, and checkout were visually inspected.
- Mobile checkout and navigation were checked at 390 pixels wide.
- A 2000 × 1000 PNG upload became a **900 × 450 JPEG product image** and a **256 × 256 JPEG avatar**.

HTTP checks covered protected routes, CSRF, invalid/valid login, all screens, customer validation and CRUD, product validation and CRUD, upload preparation/display, non-image rejection, escaped HTML, staff CRUD and avatar upload, insufficient stock, server-side pricing and staff assignment, duplicate-sale prevention, stale stock edit protection, self-deletion prevention, history preservation, and logout.

Database tests cover successful sales, exact totals, walk-in sales, stock exhaustion, overselling, duplicate requests, archived/deleted records, invalid staff and quantities, total-overflow rollback, retained historical relationships, and rollback after a deliberately forced database insert failure.

Test-only records were removed after checks; the original demo inventory and 17 sales were retained. This is local verification, not a public deployment.

