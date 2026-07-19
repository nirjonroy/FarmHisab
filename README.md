# FarmHisab

FarmHisab is a Laravel 9 poultry farm business management system. The current implementation includes the project foundation, web authentication, Spatie roles and permissions, a role-based dashboard shell, and basic admin user management.

## Stack

- Laravel 9
- PHP 8.0+
- MySQL
- Blade
- Bootstrap 5
- Vanilla JavaScript
- Vite
- Laravel Sanctum
- Spatie Laravel Permission

## Local Installation

```bash
cd "D:\xampp\htdocs\firm management"
composer install
npm install
copy .env.example .env
php artisan key:generate
```

Configure MySQL in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=farmhisab
DB_USERNAME=root
DB_PASSWORD=
```

Create the database manually:

```sql
CREATE DATABASE farmhisab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Run migrations and seed roles:

```bash
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
```

Set default admin environment values before seeding the default admin:

```env
DEFAULT_ADMIN_NAME="FarmHisab Admin"
DEFAULT_ADMIN_EMAIL=admin@example.com
DEFAULT_ADMIN_PASSWORD=change-this-password
```

Then run:

```bash
php artisan db:seed --class=DefaultAdminSeeder
php artisan permission:cache-reset
```

Build assets and start the app:

```bash
npm run build
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

## Development Commands

```bash
npm run dev
npm run build
php artisan optimize:clear
php artisan route:list
php artisan test
php artisan about
composer audit
npm audit
```

## Authentication

FarmHisab includes manual Laravel web authentication using Blade and Bootstrap 5:

- login
- logout
- registration
- forgot password
- reset password
- remember me
- authenticated dashboard
- inactive-user login blocking

New self-registered users receive the `worker` role after role seeding exists.

## Roles vs Permissions

Roles group users operationally:

- `admin`
- `manager`
- `worker`

Permissions grant feature access. Code should authorize by permission rather than spreading role checks through controllers and Blade files.

Permission naming uses `module.action`, for example:

- `dashboard.view`
- `users.create`
- `feed.manage`
- `profit-reports.view`

## Role-Permission Matrix

Admin receives every permission.

Manager receives:

- `dashboard.view`
- `farms.view`
- `farms.manage`
- `batches.view`
- `batches.manage`
- `daily-records.view`
- `daily-records.create`
- `daily-records.update`
- `feed.view`
- `feed.manage`
- `feed-usage.create`
- `medicine.view`
- `medicine.manage`
- `vaccinations.manage`
- `mortality.view`
- `mortality.create`
- `mortality.update`
- `weights.view`
- `weights.create`
- `weights.update`
- `expenses.view`
- `expenses.manage`
- `sales.view`
- `sales.manage`
- `inventory.view`
- `inventory.manage`
- `reports.view`
- `profit-reports.view`

Worker receives:

- `dashboard.view`
- `batches.view`
- `daily-records.view`
- `daily-records.create`
- `feed.view`
- `feed-usage.create`
- `medicine.view`
- `mortality.view`
- `mortality.create`
- `weights.view`
- `weights.create`

## Adding Permissions Later

1. Add the permission name to `App\Support\AccessControl::PERMISSIONS`.
2. Add it to the correct role entry in `AccessControl::ROLE_PERMISSIONS`.
3. Run:

```bash
php artisan db:seed --class=RolePermissionSeeder
php artisan permission:cache-reset
```

## Authorization Usage

Routes:

```php
Route::middleware(['auth', 'permission:users.view'])->group(...);
```

Controllers:

```php
$this->authorize('delete', $user);
```

Blade:

```blade
@can('reports.view')
    ...
@endcan
```

Policies should handle record-specific rules, such as preventing removal of the last active admin.

## Admin User Management

Admin users with the required permissions can:

- list users
- create users
- edit name and email
- assign one operational role using `syncRoles()`
- change passwords
- activate and deactivate users
- delete users, subject to policy safeguards

Passwords are hashed and never displayed.

## Android API

Laravel Sanctum is installed for future Android token authentication. Android API authorization will reuse the same permission model. No Android business API endpoints are implemented yet.
