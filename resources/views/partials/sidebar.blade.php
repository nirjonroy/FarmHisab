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
    @can('batches.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'batches') }}">{{ __('modules.batches') }}</a>
    @endcan
    @can('daily-records.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'daily-records') }}">{{ __('modules.daily_records') }}</a>
    @endcan
    @can('feed.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'feed') }}">{{ __('modules.feed') }}</a>
    @elsecan('feed-usage.create')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'feed-usage') }}">{{ __('modules.feed_usage') }}</a>
    @endcan
    @can('medicine.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'medicine') }}">{{ __('modules.medicine_vaccines') }}</a>
    @endcan
    @can('mortality.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'mortality') }}">{{ __('modules.mortality') }}</a>
    @endcan
    @can('weights.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'weights') }}">{{ __('modules.weight_records') }}</a>
    @endcan
    @can('expenses.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'expenses') }}">{{ __('modules.expenses') }}</a>
    @endcan
    @can('sales.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'sales') }}">{{ __('modules.sales') }}</a>
    @endcan
    @can('inventory.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'inventory') }}">{{ __('modules.inventory') }}</a>
    @endcan
    @can('reports.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'reports') }}">{{ __('modules.reports') }}</a>
    @endcan
    @can('settings.manage')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'settings') }}">{{ __('modules.settings') }}</a>
    @endcan
</nav>
