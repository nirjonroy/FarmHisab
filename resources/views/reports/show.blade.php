@extends('layouts.app')

@section('title', __($report['label']).' - '.__('common.app_name'))
@section('page_title', __($report['label']))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">{{ __('modules.reports') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __($report['label']) }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm report-shell-card">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <span class="report-eyebrow">{{ __('modules.reports') }}</span>
                    <h2 class="h4 mb-2">{{ __($report['label']) }}</h2>
                    <p class="text-muted mb-0">{{ __($report['description']) }}</p>
                </div>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-success align-self-start">{{ __('reports.all_reports') }}</a>
            </div>

            <form class="row g-3 report-filter-panel">
                <div class="col-md-4">
                    <label class="form-label">{{ __('reports.from_date') }}</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('reports.to_date') }}</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('reports.batch') }}</label>
                    <select class="form-select">
                        <option>{{ __('reports.all_batches') }}</option>
                    </select>
                </div>
                <div class="col-12 d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-success">{{ __('reports.generate') }}</button>
                    <button type="button" class="btn btn-outline-secondary">{{ __('reports.export_pdf') }}</button>
                    <button type="button" class="btn btn-outline-secondary">{{ __('reports.export_excel') }}</button>
                </div>
            </form>

            <div class="report-empty-state mt-4">
                <h3>{{ __('reports.report_ready') }}</h3>
                <p>{{ __('reports.report_ready_description') }}</p>
            </div>
        </div>
    </div>
@endsection
