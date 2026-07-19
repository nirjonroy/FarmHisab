# FarmHisab Architecture

FarmHisab is a Laravel 9 application planned for both a Blade-based web dashboard and a future Android REST API.

## Laravel Web Application

The application uses standard Laravel conventions for routing, middleware, service providers, configuration, validation, authorization, queued jobs, events, and tests. Controllers should stay thin and delegate business workflows to services.

## MySQL Database

MySQL is the configured application database. Step 2 adds only authentication and authorization structures: Spatie permission tables and an `is_active` flag on `users`.

Farm setup data supports multiple farm domains, not only poultry. `farm_categories` stores a two-level hierarchy for top-level categories such as Poultry, Livestock, and Aquaculture, with direct child categories such as Broiler, Cattle, and Fish. Categories and breed/species records are separate concepts: categories describe the operational farming type, while future breed/species records will describe more specific biological classifications. Future batch records are expected to reference a farm category.

## Blade Web Dashboard

Blade renders the web dashboard. Bootstrap 5 is installed through Vite, and vanilla JavaScript is used through the existing Vite entrypoint. No Tailwind, React, Vue, Livewire, or Inertia stack is used.

## Localization

FarmHisab supports Bengali (`bn`) and English (`en`) for static interface text. Bengali is the default locale and English is the fallback locale. Static labels, navigation, authentication text, dashboard labels, and shared messages should use Laravel language files with keys such as `__('navigation.dashboard')` or `__('common.save')`.

Locale is resolved for web requests from the authenticated user's saved `users.locale` value, then the session `locale`, then the configured default locale. The language switch route writes the selected locale to the session and, for authenticated users, to `users.locale`.

Static interface text and dynamic database records are separate translation concerns:

- Static interface text belongs in Laravel language files.
- Dynamic database records should later use separate bilingual fields such as `name_en`, `name_bn`, `description_en`, and `description_bn`.

Future Android APIs may return both stored values, such as `name_en` and `name_bn`, plus a computed `display_name` based on the requested locale. These dynamic bilingual database fields are not implemented yet.

## Authentication

Web authentication uses Laravel sessions, CSRF protection, password hashing, guest/auth middleware, session regeneration after login, and standard password reset brokers.

Inactive users are blocked during login by the login Form Request.

## Roles and Permissions

Spatie Laravel Permission is used because it provides tested role and permission models, middleware, Blade directives, cache handling, and pivot tables without custom role columns.

Roles describe operational groups: `admin`, `manager`, and `worker`.

Permissions describe feature access and use the `module.action` convention, for example `users.view`, `users.create`, and `reports.view`.

Routes and Blade templates should use permissions:

```php
Route::middleware(['auth', 'permission:users.view'])->group(...);
```

```blade
@can('reports.view')
    ...
@endcan
```

Role checks should be limited to cases where the role itself is displayed, such as a role badge.

## Service and Repository Layers

User management uses `App\Services\UserService` for transaction-backed multi-step user creation, updates, role syncing, and status changes. Future business modules should place workflows in `app/Services` and reusable data access in `app/Repositories`.

## Policies

`UserPolicy` handles record-level safeguards:

- users cannot delete themselves
- users cannot deactivate themselves
- the last active admin cannot be deleted
- the last active admin cannot be deactivated
- the last active admin cannot lose the admin role

## Web and API Route Separation

Web routes belong in `routes/web.php` and return Blade views or redirects. API routes belong in `routes/api.php` and return JSON responses.

Android API authorization will later reuse the same permission model with Sanctum tokens. No Android business API endpoints are created yet.
