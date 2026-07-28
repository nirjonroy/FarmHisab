<nav class="list-group list-group-flush">
    @can('dashboard.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">{{ __('modules.dashboard') }}</a>
    @endcan
    @can('users.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">{{ __('modules.users') }}</a>
    @endcan
    @can('farms.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('farms.*') ? 'active' : '' }}" href="{{ route('farms.index') }}">{{ __('modules.farms') }}</a>
        <a class="list-group-item list-group-item-action {{ request()->routeIs('sheds.*') ? 'active' : '' }}" href="{{ route('sheds.index') }}">{{ __('modules.sheds') }}</a>
    @endcan
    @can('farm-categories.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('farm-categories.*') ? 'active' : '' }}" href="{{ route('farm-categories.index') }}">{{ __('modules.farm_categories') }}</a>
    @endcan
    @can('farm-varieties.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('farm-varieties.*') ? 'active' : '' }}" href="{{ route('farm-varieties.index') }}">{{ __('modules.farm_varieties') }}</a>
    @endcan
    @can('measurement-units.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('measurement-units.*') ? 'active' : '' }}" href="{{ route('measurement-units.index') }}">{{ __('modules.measurement_units') }}</a>
    @endcan
    @can('products.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">{{ __('modules.products') }}</a>
    @endcan
    @can('batches.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('batches.*') ? 'active' : '' }}" href="{{ route('batches.index') }}">{{ __('modules.batches') }}</a>
    @endcan
    @can('daily-records.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('daily-records.*') ? 'active' : '' }}" href="{{ route('daily-records.index') }}">{{ __('modules.daily_records') }}</a>
    @endcan
    @can('feed.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('feed.*') ? 'active' : '' }}" href="{{ route('feed.index') }}">{{ __('modules.feed') }}</a>
    @elsecan('feed-usage.create')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('feed.*') ? 'active' : '' }}" href="{{ route('feed.index') }}">{{ __('modules.feed_usage') }}</a>
    @endcan
    @can('medicine.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('medicine.*') ? 'active' : '' }}" href="{{ route('medicine.index') }}">{{ __('modules.medicine_vaccines') }}</a>
    @endcan
    @can('mortality.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('mortality.*') ? 'active' : '' }}" href="{{ route('mortality.index') }}">{{ __('modules.mortality') }}</a>
    @endcan
    @can('weights.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('weights.*') ? 'active' : '' }}" href="{{ route('weights.index') }}">{{ __('modules.weight_records') }}</a>
    @endcan
    @can('expenses.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">{{ __('modules.expenses') }}</a>
    @endcan
    @can('sales.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">{{ __('modules.sales') }}</a>
    @endcan
    @can('inventory.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.index') }}">{{ __('modules.inventory') }}</a>
    @endcan
    @can('reports.view')
        @php($reportsOpen = request()->routeIs('reports.*'))
        <details class="sidebar-group" {{ $reportsOpen ? 'open' : '' }}>
            <summary class="list-group-item list-group-item-action sidebar-group-toggle {{ $reportsOpen ? 'active' : '' }}">
                <span>{{ __('modules.reports') }}</span>
                <span class="sidebar-group-chevron" aria-hidden="true"></span>
            </summary>
            <div class="sidebar-submenu">
                <a class="sidebar-submenu-item {{ request()->routeIs('reports.index') ? 'active' : '' }}" href="{{ route('reports.index') }}">{{ __('reports.all_reports') }}</a>
                @foreach (config('reports.items', []) as $key => $report)
                    <a class="sidebar-submenu-item {{ request()->routeIs('reports.show') && request()->route('report') === $key ? 'active' : '' }}" href="{{ route('reports.show', $key) }}">
                        {{ __($report['label']) }}
                    </a>
                @endforeach
            </div>
        </details>
    @endcan
    @can('settings.manage')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.edit') }}">{{ __('modules.settings') }}</a>
    @endcan
</nav>
