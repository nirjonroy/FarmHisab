<nav class="list-group list-group-flush">
    @can('dashboard.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
    @endcan
    @can('users.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Users</a>
    @endcan
    @can('farms.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('farms.*') ? 'active' : '' }}" href="{{ route('farms.index') }}">Farms</a>
        <a class="list-group-item list-group-item-action {{ request()->routeIs('sheds.*') ? 'active' : '' }}" href="{{ route('sheds.index') }}">Sheds</a>
    @endcan
    @can('farm-categories.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('farm-categories.*') ? 'active' : '' }}" href="{{ route('farm-categories.index') }}">Farm Categories</a>
    @endcan
    @can('batches.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'batches') }}">Batches</a>
    @endcan
    @can('daily-records.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'daily-records') }}">Daily records</a>
    @endcan
    @can('feed.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'feed') }}">Feed</a>
    @elsecan('feed-usage.create')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'feed-usage') }}">Feed usage</a>
    @endcan
    @can('medicine.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'medicine') }}">Medicine and vaccines</a>
    @endcan
    @can('mortality.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'mortality') }}">Mortality</a>
    @endcan
    @can('weights.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'weights') }}">Weight records</a>
    @endcan
    @can('expenses.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'expenses') }}">Expenses</a>
    @endcan
    @can('sales.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'sales') }}">Sales</a>
    @endcan
    @can('inventory.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'inventory') }}">Inventory</a>
    @endcan
    @can('reports.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'reports') }}">Reports</a>
    @endcan
    @can('settings.manage')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'settings') }}">Settings</a>
    @endcan
</nav>
