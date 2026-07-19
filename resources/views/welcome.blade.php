<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'FarmHisab') }}</title>
    @vite('resources/js/app.js')
</head>
<body>
    <main class="min-vh-100 d-flex align-items-center">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="border bg-white rounded-3 p-4 p-md-5 shadow-sm">
                        <p class="text-uppercase text-success fw-semibold mb-2">Project foundation</p>
                        <h1 class="display-6 fw-bold mb-3">FarmHisab</h1>
                        <p class="lead text-secondary mb-4">
                            Laravel 9 foundation for a poultry farm business management web application and future Android REST API.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge text-bg-success">Laravel</span>
                            <span class="badge text-bg-primary">MySQL</span>
                            <span class="badge text-bg-info">Blade</span>
                            <span class="badge text-bg-dark">Bootstrap 5</span>
                            <span class="badge text-bg-secondary">Vanilla JavaScript</span>
                            <span class="badge text-bg-warning">Sanctum</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
