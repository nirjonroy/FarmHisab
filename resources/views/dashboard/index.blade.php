@extends('layouts.app')

@section('title', __('dashboard.title').' - '.__('common.app_name'))
@section('page_title', __('dashboard.title'))

@section('content')
    <div class="dashboard-hero mb-4">
        <div>
            <div class="eyebrow">{{ __('common.app_name') }}</div>
            <h2>{{ __('dashboard.title') }}</h2>
        </div>
        <div class="hero-pill">{{ now()->format('Y-m-d') }}</div>
    </div>

    <div class="row g-3 metric-grid">
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
@endsection
