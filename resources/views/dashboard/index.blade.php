@extends('layouts.app')

@section('title', __('dashboard.title').' - '.($appName ?? __('common.app_name')))
@section('page_title', __('dashboard.title'))

@section('content')
    <div class="dashboard-hero mb-4">
        <div>
            <div class="eyebrow">{{ $appName ?? __('common.app_name') }}</div>
            <h2>{{ __('dashboard.title') }}</h2>
            <p>{{ __('dashboard.subtitle') }}</p>
        </div>
        <div class="hero-pill">{{ now()->format('Y-m-d') }}</div>
    </div>

    <div class="row g-3 metric-grid mb-4">
        @foreach ($metrics as $label => $value)
            <div class="col-sm-6 col-xl-3">
                <div class="card metric-card h-100">
                    <div class="card-body">
                        <div class="metric-label">{{ __($label) }}</div>
                        <div class="metric-value">{{ $value }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        @foreach ($financeMetrics as $label => $value)
            <div class="col-sm-6 col-xl-3">
                <div class="card metric-card dashboard-finance-card h-100">
                    <div class="card-body">
                        <div class="metric-label">{{ __($label) }}</div>
                        <div class="metric-value">{{ $value }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm dashboard-panel h-100">
                <div class="card-body">
                    <div class="dashboard-panel-header">
                        <div>
                            <h3>{{ __('dashboard.revenue_vs_expenses') }}</h3>
                            <p>{{ __('dashboard.last_six_months') }}</p>
                        </div>
                    </div>
                    <div class="dashboard-bar-chart">
                        @foreach ($monthlyTrend['items'] as $item)
                            <div class="dashboard-chart-month">
                                <div class="dashboard-bars">
                                    <span class="dashboard-bar revenue" style="height: {{ max(4, ($item['revenue'] / $monthlyTrend['max']) * 100) }}%" title="{{ __('dashboard.total_revenue') }}: Tk{{ number_format($item['revenue'], 2) }}"></span>
                                    <span class="dashboard-bar expenses" style="height: {{ max(4, ($item['expenses'] / $monthlyTrend['max']) * 100) }}%" title="{{ __('dashboard.total_expenses') }}: Tk{{ number_format($item['expenses'], 2) }}"></span>
                                </div>
                                <span class="dashboard-chart-label">{{ $item['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="dashboard-chart-legend">
                        <span><i class="legend-dot revenue"></i>{{ __('dashboard.total_revenue') }}</span>
                        <span><i class="legend-dot expenses"></i>{{ __('dashboard.total_expenses') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm dashboard-panel h-100">
                <div class="card-body">
                    <div class="dashboard-panel-header">
                        <div>
                            <h3>{{ __('dashboard.cost_breakdown') }}</h3>
                            <p>{{ __('dashboard.investment_distribution') }}</p>
                        </div>
                    </div>
                    <div class="dashboard-progress-list">
                        @foreach ($costBreakdown as $item)
                            <div class="dashboard-progress-item">
                                <div class="d-flex justify-content-between gap-3">
                                    <span>{{ __($item['label']) }}</span>
                                    <strong>Tk{{ number_format($item['amount'], 2) }}</strong>
                                </div>
                                <div class="dashboard-progress-track">
                                    <span style="width: {{ $item['percentage'] }}%"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm dashboard-panel h-100">
                <div class="card-body">
                    <div class="dashboard-panel-header">
                        <div>
                            <h3>{{ __('dashboard.operations_snapshot') }}</h3>
                            <p>{{ __('dashboard.live_module_totals') }}</p>
                        </div>
                    </div>
                    <div class="dashboard-stat-list">
                        @foreach ($operationMetrics as $label => $value)
                            <div class="dashboard-stat-row">
                                <span>{{ __($label) }}</span>
                                <strong>{{ $value }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card border-0 shadow-sm dashboard-panel h-100">
                <div class="card-body">
                    <div class="dashboard-panel-header">
                        <div>
                            <h3>{{ __('dashboard.latest_active_batches') }}</h3>
                            <p>{{ __('dashboard.batch_health_overview') }}</p>
                        </div>
                        <a href="{{ route('batches.index') }}" class="btn btn-outline-success btn-sm">{{ __('dashboard.view_all_batches') }}</a>
                    </div>

                    <div class="table-responsive dashboard-table-wrap">
                        <table class="table align-middle mb-0 dashboard-table">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.batch') }}</th>
                                    <th>{{ __('dashboard.farm') }}</th>
                                    <th class="text-end">{{ __('dashboard.current_birds') }}</th>
                                    <th class="text-end">{{ __('dashboard.investment') }}</th>
                                    <th class="text-end">{{ __('dashboard.profit_loss') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestBatches as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('batches.show', $item['batch']) }}" class="fw-bold">{{ $item['batch']->batch_no }}</a>
                                            <div class="text-muted small">{{ $item['batch']->batch_name }}</div>
                                        </td>
                                        <td>{{ $item['batch']->farm?->name ?? __('common.not_set') }}</td>
                                        <td class="text-end">{{ number_format($item['current_birds']) }}</td>
                                        <td class="text-end">Tk{{ number_format($item['investment'], 2) }}</td>
                                        <td class="text-end">
                                            <span class="badge {{ $item['profit'] >= 0 ? 'text-bg-success' : 'text-bg-danger' }}">
                                                Tk{{ number_format($item['profit'], 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">{{ __('dashboard.no_active_batches') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
