@extends('layouts.app')

@section('title', __('modules.reports').' - '.__('common.app_name'))
@section('page_title', __('modules.reports'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('modules.reports') }}</li>
@endsection

@section('content')
    <div class="report-hero mb-4">
        <div>
            <span class="report-eyebrow">{{ __('common.app_name') }}</span>
            <h2>{{ __('reports.report_center') }}</h2>
            <p>{{ __('reports.report_center_description') }}</p>
        </div>
    </div>

    <div class="row g-3">
        @foreach ($reports as $key => $report)
            <div class="col-md-6 col-xl-4">
                <a class="report-card" href="{{ route('reports.show', $key) }}">
                    <span class="report-card-title">{{ __($report['label']) }}</span>
                    <span class="report-card-text">{{ __($report['description']) }}</span>
                    <span class="report-card-action">{{ __('reports.open_report') }}</span>
                </a>
            </div>
        @endforeach
    </div>
@endsection
