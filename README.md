# FarmHisab

FarmHisab is a Laravel 9 poultry farm business management system foundation. This step prepares the project for a Blade web application and a future Android REST API using Laravel Sanctum.

## Stack

- Laravel 9
- PHP 8.0+
- MySQL
- Blade
- Bootstrap 5
- Vanilla JavaScript
- Vite
- Laravel Sanctum

## Local Installation

1. Clone or open the project directory.

```bash
cd "D:\xampp\htdocs\firm management"
```

2. Install PHP dependencies.

```bash
composer install
```

3. Install frontend dependencies.

```bash
npm install
```

4. Create the environment file if it does not exist.

```bash
copy .env.example .env
```

5. Generate the Laravel application key.

```bash
php artisan key:generate
```

6. Configure MySQL in `.env`.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=farmhisab
DB_USERNAME=root
DB_PASSWORD=
```

7. Create the MySQL database manually.

```sql
CREATE DATABASE farmhisab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

8. Build frontend assets.

```bash
npm run build
```

9. Start the local Laravel server.

```bash
php artisan serve
```

10. Open the application.

```text
http://127.0.0.1:8000
```

## Development Commands

Run Vite during frontend development:

```bash
npm run dev
```

Run tests:

```bash
php artisan test
```

Show project information:

```bash
php artisan about
```

Review Composer package security advisories:

```bash
composer audit
```

Review npm package security advisories:

```bash
npm audit
```
