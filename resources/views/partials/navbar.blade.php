<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top shadow-sm">
    <div class="container-fluid">
        <button class="btn btn-success d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">FarmHisab</a>

        <div class="dropdown ms-auto">
            <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ auth()->user()->name }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text text-muted">{{ auth()->user()->email }}</span></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
