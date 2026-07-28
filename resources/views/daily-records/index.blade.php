@extends('layouts.app')

@section('title', __('daily_records.title').' - '.__('common.app_name'))
@section('page_title', __('daily_records.title'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('daily_records.title') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('daily-records.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label">{{ __('common.search') }}</label>
                    <input id="search" type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('daily_records.search_placeholder') }}">
                </div>
                <div class="col-md-3">
                    <label for="batch_id" class="form-label">{{ __('daily_records.batch') }}</label>
                    <select id="batch_id" name="batch_id" class="form-select">
                        <option value="">{{ __('daily_records.all_batches') }}</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}" @selected((int) $batchId === $batch->id)>{{ $batch->batch_no }} - {{ $batch->batch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">{{ __('daily_records.date_from') }}</label>
                    <input id="date_from" type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">{{ __('daily_records.date_to') }}</label>
                    <input id="date_to" type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-outline-success">{{ __('common.filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        @can('daily-records.create')
            <a href="{{ route('daily-records.create') }}" class="btn btn-success">{{ __('daily_records.add_record') }}</a>
        @endcan
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('daily_records.record_date') }}</th>
                        <th>{{ __('daily_records.batch') }}</th>
                        <th>{{ __('daily_records.opening_birds') }}</th>
                        <th>{{ __('daily_records.mortality_birds') }}</th>
                        <th>{{ __('daily_records.sold_birds') }}</th>
                        <th>{{ __('daily_records.closing_birds') }}</th>
                        <th>{{ __('daily_records.feed_consumed_bags') }}</th>
                        <th class="text-end">{{ __('daily_records.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $record->record_date->format('Y-m-d') }}</td>
                            <td>
                                <div class="fw-semibold">{{ $record->batch?->batch_no }}</div>
                                <div class="text-muted small">{{ $record->batch?->batch_name }}</div>
                            </td>
                            <td>{{ number_format($record->opening_birds) }}</td>
                            <td><span class="badge text-bg-danger">{{ number_format($record->mortality_birds + $record->culled_birds) }}</span></td>
                            <td>{{ number_format($record->sold_birds) }}</td>
                            <td><span class="badge text-bg-success">{{ number_format($record->closing_birds) }}</span></td>
                            <td>{{ number_format((float) $record->feed_consumed_bags, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('daily-records.show', $record) }}" class="btn btn-sm btn-outline-secondary">{{ __('common.view') }}</a>
                                @can('daily-records.update')
                                    <a href="{{ route('daily-records.edit', $record) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">{{ __('daily_records.no_records_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($records->hasPages())
            <div class="card-footer bg-white">{{ $records->links() }}</div>
        @endif
    </div>
@endsection
