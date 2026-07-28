@extends('layouts.app')

@section('title', __('feed.title').' - '.__('common.app_name'))
@section('page_title', __('feed.title'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('feed.title') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('feed.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label">{{ __('common.search') }}</label>
                    <input id="search" type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('feed.search_placeholder') }}">
                </div>
                <div class="col-md-3">
                    <label for="batch_id" class="form-label">{{ __('feed.batch') }}</label>
                    <select id="batch_id" name="batch_id" class="form-select">
                        <option value="">{{ __('feed.all_batches') }}</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}" @selected((int) $batchId === $batch->id)>{{ $batch->batch_no }} - {{ $batch->batch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">{{ __('feed.date_from') }}</label>
                    <input id="date_from" type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">{{ __('feed.date_to') }}</label>
                    <input id="date_to" type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-outline-success">{{ __('common.filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        @can('feed-usage.create')
            <a href="{{ route('feed.create') }}" class="btn btn-success">{{ __('feed.add_record') }}</a>
        @elsecan('feed.manage')
            <a href="{{ route('feed.create') }}" class="btn btn-success">{{ __('feed.add_record') }}</a>
        @endcan
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('feed.record_date') }}</th>
                        <th>{{ __('feed.batch') }}</th>
                        <th>{{ __('feed.feed_name') }}</th>
                        <th>{{ __('feed.bags') }}</th>
                        <th>{{ __('feed.quantity_kg') }}</th>
                        <th>{{ __('feed.total_cost') }}</th>
                        <th class="text-end">{{ __('feed.actions') }}</th>
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
                            <td>{{ $record->product?->display_name ?: $record->feed_name }}</td>
                            <td>{{ number_format((float) $record->bags, 2) }}</td>
                            <td>{{ number_format((float) $record->quantity_kg, 2) }}</td>
                            <td>Tk{{ number_format((float) $record->total_cost, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('feed.show', $record) }}" class="btn btn-sm btn-outline-secondary">{{ __('common.view') }}</a>
                                @can('feed.manage')
                                    <a href="{{ route('feed.edit', $record) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">{{ __('feed.no_records_found') }}</td>
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
