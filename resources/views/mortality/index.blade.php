@extends('layouts.app')

@section('title', __('mortality.title').' - '.__('common.app_name'))
@section('page_title', __('mortality.title'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('mortality.title') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('mortality.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label">{{ __('common.search') }}</label>
                    <input id="search" type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('mortality.search_placeholder') }}">
                </div>
                <div class="col-md-3">
                    <label for="batch_id" class="form-label">{{ __('mortality.batch') }}</label>
                    <select id="batch_id" name="batch_id" class="form-select">
                        <option value="">{{ __('mortality.all_batches') }}</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}" @selected((int) $batchId === $batch->id)>{{ $batch->batch_no }} - {{ $batch->batch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label">{{ __('mortality.type') }}</label>
                    <select id="type" name="type" class="form-select">
                        <option value="">{{ __('mortality.all_types') }}</option>
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">{{ __('mortality.date_from') }}</label>
                    <input id="date_from" type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">{{ __('mortality.date_to') }}</label>
                    <input id="date_to" type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-outline-success">{{ __('common.filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        @can('mortality.create')
            <a href="{{ route('mortality.create') }}" class="btn btn-success">{{ __('mortality.add_record') }}</a>
        @endcan
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('mortality.record_date') }}</th>
                        <th>{{ __('mortality.batch') }}</th>
                        <th>{{ __('mortality.type') }}</th>
                        <th>{{ __('mortality.birds') }}</th>
                        <th>{{ __('mortality.cause') }}</th>
                        <th>{{ __('mortality.action_taken') }}</th>
                        <th class="text-end">{{ __('mortality.actions') }}</th>
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
                            <td><span class="badge {{ $record->type->value === 'mortality' ? 'text-bg-danger' : 'text-bg-warning' }}">{{ $record->type->label() }}</span></td>
                            <td>{{ number_format($record->birds) }}</td>
                            <td>{{ $record->cause ?: __('common.not_set') }}</td>
                            <td>{{ $record->action_taken ?: __('common.not_set') }}</td>
                            <td class="text-end">
                                <a href="{{ route('mortality.show', $record) }}" class="btn btn-sm btn-outline-secondary">{{ __('common.view') }}</a>
                                @can('mortality.update')
                                    <a href="{{ route('mortality.edit', $record) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">{{ __('mortality.no_records_found') }}</td>
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
