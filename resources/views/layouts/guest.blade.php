<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'FarmHisab') }}</title>
    @vite('resources/js/app.js')
</head>
<body>
    <main class="min-vh-100 d-flex align-items-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-5">
                    <div class="text-center mb-4">
                        <a href="{{ url('/') }}" class="text-decoration-none">
                            <span class="fs-3 fw-bold text-success">FarmHisab</span>
                        </a>
                    </div>
                    @include('partials.flash-messages')
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
