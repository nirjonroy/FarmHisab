<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('common.app_name'))</title>
    @vite('resources/js/app.js')
</head>
<body class="app-shell">
    @include('partials.navbar')

    <div class="container-fluid app-frame">
        <div class="row g-0">
            <aside class="col-xl-2 col-lg-3 d-none d-lg-block app-sidebar">
                <div class="sidebar-panel">
                    <div class="sidebar-label">{{ __('common.app_name') }}</div>
                    @include('partials.sidebar')
                </div>
            </aside>

            <main class="col-xl-10 col-lg-9 ms-sm-auto app-main">
                <div class="page-header">
                    <div class="min-w-0">
                        <h1 class="page-title">@yield('page_title', __('modules.dashboard'))</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb page-breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('modules.dashboard') }}</a></li>
                                @yield('breadcrumbs')
                            </ol>
                        </nav>
                    </div>
                    <span class="role-badge">{{ auth()->user()->roles->pluck('name')->join(', ') ?: __('common.no_role') }}</span>
                </div>

                @include('partials.flash-messages')
                <div class="page-content">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header mobile-sidebar-header">
            <div>
                <h5 class="offcanvas-title" id="mobileSidebarLabel">{{ __('common.app_name') }}</h5>
                <div class="mobile-sidebar-subtitle">{{ auth()->user()->roles->pluck('name')->join(', ') ?: __('common.no_role') }}</div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="{{ __('common.close') }}"></button>
        </div>
        <div class="offcanvas-body mobile-sidebar-body">
            @include('partials.sidebar')
        </div>
    </div>
    @stack('scripts')
</body>
</html>
