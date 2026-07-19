# FarmHisab Architecture

FarmHisab is a Laravel 9 application planned for both a Blade-based web dashboard and a future Android REST API.

## Laravel Web Application

The application uses standard Laravel conventions for routing, middleware, service providers, configuration, validation, authorization, queued jobs, events, and tests. Business code should remain organized by responsibility instead of being placed directly into route closures.

## MySQL Database

MySQL is the configured relational database. Future migrations will define poultry farm entities, financial records, inventory data, and reporting tables. Database credentials are managed through `.env`.

## Blade Web Dashboard

Blade will be used for server-rendered web pages. Bootstrap 5 is installed through Vite and should be used for the initial dashboard UI foundation with small amounts of vanilla JavaScript where needed.

## REST API for Android

Android integration will use versioned API routes in `routes/api.php`. API controllers and resources should be introduced only when mobile features are implemented.

## Laravel Sanctum Authentication

Laravel Sanctum is installed and configured for future API token authentication. Web authentication can use Laravel sessions, while Android clients can use Sanctum personal access tokens after the authentication module is built.

## Service and Repository Layers

Business workflows should live in `app/Services`. Data access abstractions that are useful across modules should live in `app/Repositories`. Keep controllers thin by delegating business operations to services.

## Web and API Route Separation

Web routes belong in `routes/web.php` and return Blade views or redirects. API routes belong in `routes/api.php` and return JSON responses. Authentication, middleware, validation, and response formats should stay separate between web and API surfaces.
