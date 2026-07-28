<nav class="navbar navbar-expand-lg navbar-dark sticky-top app-navbar">
    <div class="container-fluid app-navbar-inner">
        <button class="btn nav-icon-button d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand app-brand" href="{{ route('dashboard') }}">{{ __('common.app_name') }}</a>

        <div class="topbar-actions">
            @include('partials.language-switcher', ['buttonClass' => 'btn-outline-light app-topbar-button'])

            <div class="dropdown app-dropdown">
                <button class="btn user-menu-button dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-auto-close="outside" aria-expanded="false">
                    <span class="user-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    <span class="user-menu-name d-none d-sm-inline">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end app-dropdown-menu">
                    <li><span class="dropdown-header-text">{{ auth()->user()->name }}</span></li>
                    <li><span class="dropdown-item-text text-muted">{{ auth()->user()->email }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">{{ __('navigation.logout') }}</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
