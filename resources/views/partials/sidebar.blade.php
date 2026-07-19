<nav class="list-group list-group-flush">
    @can('dashboard.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">{{ __('navigation.dashboard') }}</a>
    @endcan
    @can('users.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">{{ __('navigation.users') }}</a>
    @endcan
    @can('farms.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('farms.*') ? 'active' : '' }}" href="{{ route('farms.index') }}">{{ __('navigation.farms') }}</a>
        <a class="list-group-item list-group-item-action {{ request()->routeIs('sheds.*') ? 'active' : '' }}" href="{{ route('sheds.index') }}">{{ __('navigation.sheds') }}</a>
    @endcan
    @can('farm-categories.view')
        <a class="list-group-item list-group-item-action {{ request()->routeIs('farm-categories.*') ? 'active' : '' }}" href="{{ route('farm-categories.index') }}">{{ __('navigation.farm_categories') }}</a>
    @endcan
    @can('batches.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'batches') }}">{{ __('navigation.batches') }}</a>
    @endcan
    @can('daily-records.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'daily-records') }}">{{ __('navigation.daily_records') }}</a>
    @endcan
    @can('feed.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'feed') }}">{{ __('navigation.feed') }}</a>
    @elsecan('feed-usage.create')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'feed-usage') }}">{{ __('navigation.feed_usage') }}</a>
    @endcan
    @can('medicine.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'medicine') }}">{{ __('navigation.medicine_vaccines') }}</a>
    @endcan
    @can('mortality.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'mortality') }}">{{ __('navigation.mortality') }}</a>
    @endcan
    @can('weights.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'weights') }}">{{ __('navigation.weight_records') }}</a>
    @endcan
    @can('expenses.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'expenses') }}">{{ __('navigation.expenses') }}</a>
    @endcan
    @can('sales.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'sales') }}">{{ __('navigation.sales') }}</a>
    @endcan
    @can('inventory.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'inventory') }}">{{ __('navigation.inventory') }}</a>
    @endcan
    @can('reports.view')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'reports') }}">{{ __('navigation.reports') }}</a>
    @endcan
    @can('settings.manage')
        <a class="list-group-item list-group-item-action" href="{{ route('coming-soon', 'settings') }}">{{ __('navigation.settings') }}</a>
    @endcan
</nav>
