# Hosting Tamaraw POS

The local project is ready to run. A public deployment and GitHub repository have not been created; these need your chosen hosting account and repository destination.

## Production setup
1. Use PHP 8.2+ with intl, mbstring, mysqli, fileinfo, GD, and Composer. Use MySQL 8 or MariaDB 10.4+ with InnoDB.
2. Upload the source and run `composer install --no-dev --optimize-autoloader`.
3. Set the web server document root to the project's **public/** directory. Only this directory should be web-accessible.
4. Create a new database and a dedicated user. Copy `.env.example` to `.env`, replace the database credentials and base URL, set `CI_ENVIRONMENT = production`, `POS_DEMO = false`, `app.forceGlobalSecureRequests = true`, and `cookie.secure = true`.
5. For an empty real store, run `php spark migrate`. Do not import the demo SQL on production.
6. Set `POS_ADMIN_USERNAME`, `POS_ADMIN_NAME`, and a strong `POS_ADMIN_PASSWORD` temporarily in your shell environment. Run `php spark pos:create-staff`, then remove those variables. This command hashes the password.
7. Give the web-server process write permission to **writable/**. Keep uploads, logs, sessions, and cache outside the public directory. Never allow uploaded files to execute as scripts.
8. Set up HTTPS and database/upload backups. Use the same timezone for all app workers.
9. Verify login, all CRUD screens, an upload, a successful sale, an over-stock rejection, and sales history before sharing the URL.

All staff accounts have equal management access, matching the lab's staff-only authentication requirement. Add role-based permissions before using this in a store that requires cashier/admin separation. The application records single-product sales; it does not process payments, calculate taxes, or implement refunds.

## Apache
Enable `mod_rewrite` and allow the shipped `public/.htaccess`. For XAMPP's default folder layout the local URL can be `http://localhost/feu-tech-pos/public/`. Set `app.baseURL` to that exact value including the trailing slash.

For deployment, prefer a virtual host that exposes only `public/`; do not expose the project root.

## nginx
Use `root /path/to/feu-tech-pos/public;` and `try_files $uri $uri/ /index.php?$query_string;`. Configure PHP-FPM for the front controller. Protect dotfiles and never expose the app, vendor, or writable folders.

## GitHub submission
Commit source, migrations, `.env.example`, `composer.lock`, tests, SQL examples, and documentation. Exclude `.env`, `vendor/`, actual uploads, session/cache files, local databases, and logs. The included `.gitignore` handles these. Follow your instructor's requirements when adding the repository and deployed URL to your submission.

