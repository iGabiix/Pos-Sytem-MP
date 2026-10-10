# Tamaraw Campus Store — CodeIgniter 4 POS

A complete Point-of-Sale laboratory project with an FEU Tech-inspired green-and-gold interface. Built with PHP 8.2+, CodeIgniter 4.7.4, MySQL/MariaDB, and HTML/CSS/JavaScript. Original educational branding and illustrations; not an official university service.

## Start on this computer

Working folder: **C:\xampp\htdocs\feu-tech-pos**

1. Start **MySQL** in XAMPP. Start **Apache** if you want phpMyAdmin.
2. The **feu_pos** database has already been imported during setup. Do not import it again.
3. Stop any old PHP development server using **Ctrl+C** in its terminal.
4. Double-click **start-local.bat** in the project folder. Keep its terminal open.
5. Open **http://localhost:8080/**. Press **Ctrl+F5** if you see the old unstyled page.
6. Sign in with **admin** / **TamarawDemo!2026**.

The launcher enables PHP GD for uploads, sets a writable upload temporary folder, and uses the correct asset URL. It does not change system-wide PHP settings.

To use another port:

~~~powershell
powershell -NoProfile -ExecutionPolicy Bypass -File .\start-local.ps1 -Port 8082
~~~

## phpMyAdmin import on a fresh computer

**Import file: database/feu_pos.sql**

The file creates the database, all four related tables, sample inventory, customers, staff, and 17 sales. Its passwords are already hashed.

1. Start Apache and MySQL in XAMPP.
2. Open **http://localhost/phpmyadmin/** and select the server-level **Import** tab.
3. Choose **database/feu_pos.sql**, keep the format as SQL, and click **Import / Go**.
4. Confirm that **feu_pos** contains **products**, **customers**, **users**, and **sales**.
5. Copy **.env.example** to **.env** if you do not have the supplied .env.
6. Run **composer install** if the vendor directory is missing.
7. Run **start-local.bat**, then sign in with the demo credentials.

Import only into a fresh database. The script intentionally never drops or overwrites existing tables. **database/schema.sql** contains the empty schema if you do not want demo records.

## Environment configuration

The supplied .env sits in the project root, beside spark and composer.json. It uses XAMPP's default local MySQL settings:

~~~dotenv
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'
app.indexPage = ''
app.appTimezone = 'Asia/Manila'
database.default.hostname = 127.0.0.1
database.default.database = feu_pos
database.default.username = root
database.default.password = ''
database.default.DBDriver = MySQLi
database.default.DBPrefix = ''
database.default.port = 3306
security.csrfProtection = session
security.regenerate = false
security.redirect = true
POS_DEMO = true
~~~

Change the password or port if your MySQL configuration differs. Keep the trailing slash on app.baseURL.

### Apache instead of the launcher

Set **app.baseURL = 'http://localhost/feu-tech-pos/public/'**, enable **extension=gd** in the PHP configuration used by Apache, restart Apache, and open that URL. The public/.htaccess file requires mod_rewrite. For production, expose only the public directory through the server's document root.

### Migrations instead of SQL import

Create an empty feu_pos database and configure .env, then run:

~~~powershell
php spark migrate
php spark db:seed DemoSeeder
~~~

Use either SQL import or migrations plus seeding. DemoSeeder refuses to change non-empty tables and is disabled in production. For a real empty store, use the **pos:create-staff** command described in DEPLOYMENT.md.

## Included functionality

- Staff login, logout, hashed passwords, login throttling, and regenerated session IDs.
- Dashboard with actual daily revenue, transaction counts, stock watch, weekly activity, and recent sales.
- Product list/search/create/edit/archive, stock management, and prepared image uploads.
- Customer list/search/create/edit/delete.
- Staff list/search/create/edit/delete, prepared avatars, and password changes.
- Record Sale for one product, optional customer, and positive whole quantity.
- Sales History with product, customer/walk-in, staff, quantity, total, and timestamp.
- Responsive layouts, mobile navigation, confirmation dialogs, pagination, and validation feedback.

Only the demo admin password is shared. Other sample staff have randomized initial passwords; set one through Staff management if you want to sign in as them. All staff have equal management access, matching the lab requirements.

## Architecture

Request → explicit route → CSRF and authentication filters → controller → model/service → view.

| Responsibility | Main source |
| --- | --- |
| Explicit routing | app/Config/Routes.php |
| Authentication | AuthController.php, AuthFilter.php |
| Shared CRUD | ResourceController.php |
| Product/customer/staff validation | ProductController.php, CustomerController.php, StaffController.php |
| Transactional checkout | SalesController.php, app/Libraries/SaleService.php |
| Exact price arithmetic | app/Libraries/Money.php |
| Safe image preparation | app/Libraries/ImageUpload.php, MediaController.php |
| Database access | app/Models/ |
| Schema and sample data | app/Database/Migrations/, app/Database/Seeds/, database/ |
| Shared layout and interface | app/Views/, public/assets/ |

## Schema and relationships

~~~mermaid
erDiagram
    products ||--o{ sales : "is sold in"
    customers o|--o{ sales : "optionally purchases"
    users ||--o{ sales : "records"
    products {
        int id PK
        varchar name
        decimal price
        int stock_quantity
        varchar image
        datetime created_at
        datetime deleted_at
    }
    customers {
        int id PK
        varchar full_name
        varchar email
        varchar phone
        datetime created_at
        datetime deleted_at
    }
    users {
        int id PK
        varchar username UK
        varchar full_name
        varchar password
        varchar avatar
        datetime created_at
        datetime deleted_at
    }
    sales {
        int id PK
        int product_id FK
        int customer_id FK
        int sold_by FK
        int quantity
        decimal total_price
        varchar request_key UK
        datetime created_at
    }
~~~

Every required field and foreign-key relationship is retained. Two extensions support correctness:

- **deleted_at** on products/customers/users removes records from active use while retaining sales references.
- **request_key** on sales prevents a repeated submission from creating duplicate transactions or deducting inventory twice.

Deleted staff lose access on their next request. Password changes invalidate other sessions. Staff cannot delete their own account; deletion locks active staff rows to prevent concurrent deletions from removing everyone.

Historical names come from related records, so editing a name updates its display in history. Stored transaction totals are unchanged. The system records sales; it does not process payments, refunds, or taxes.

## Sale integrity

A database transaction validates active staff/customer records, then atomically reduces stock using a guarded UPDATE requiring sufficient stock and an active product. A rejected update returns a clear stock message. The product price is read while the write lock is held, multiplied using integer cents, and stored with the sale. Any insert failure rolls back the deduction.

Client-submitted totals and staff IDs are ignored. Product edits check the stock value originally displayed, preventing a stale edit form from overwriting a later sale.

## Security and uploads

CSRF protects all forms. Management and uploaded-image routes require a current authenticated staff session. Queries use bindings/query builders and output is escaped. Passwords use password_hash/password_verify and are never flashed into forms after validation failures.

Uploads allow JPEG, PNG, or WebP up to 2 MB and 4000 × 4000 pixels. Images are decoded and re-encoded as randomized JPEG files. Product images fit inside 900 × 900 pixels; avatars are center-cropped to 256 × 256. Original uploads are never published. Prepared files are stored outside public in writable/uploads and served through the authenticated media controller. PHP GD is required.

## Tests

~~~powershell
php -d extension=sqlite3 -d extension=gd vendor/phpunit/phpunit/phpunit --no-coverage
~~~

Omit extension flags for modules already enabled. Tests use an isolated SQLite memory database by default, never your feu_pos data. The sales suite was also verified on a separate MariaDB database. See VALIDATION.md.

## Deployment and submission

See DEPLOYMENT.md for production setup and GitHub submission. No public site or GitHub repository has been published. Create private staff credentials before public use; the shared demo password is for local demonstrations.

[CodeIgniter documentation](https://codeigniter.com/user_guide/) documents the framework. Google Fonts are optional; typography falls back to local system fonts offline. All illustrations, styling, and JavaScript are included.

