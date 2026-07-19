<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('common.app_name'))</title>
    @vite('resources/js/app.js')
</head>
<body>
    @include('partials.navbar')

    <div class="container-fluid">
        <div class="row">
            <aside class="col-lg-2 d-none d-lg-block sidebar border-end bg-white min-vh-100 p-0">
                @include('partials.sidebar')
            </aside>

            <main class="col-lg-10 ms-sm-auto px-md-4 py-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h1 class="h3 mb-1">@yield('page_title', __('modules.dashboard'))</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('modules.dashboard') }}</a></li>
                                @yield('breadcrumbs')
                            </ol>
                        </nav>
                    </div>
                    <span class="badge text-bg-success">{{ auth()->user()->roles->pluck('name')->join(', ') ?: __('common.no_role') }}</span>
                </div>

                @include('partials.flash-messages')
                @yield('content')
            </main>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileSidebarLabel">{{ __('common.app_name') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('common.close') }}"></button>
        </div>
        <div class="offcanvas-body p-0">
            @include('partials.sidebar')
        </div>
    </div>
</body>
</html>
