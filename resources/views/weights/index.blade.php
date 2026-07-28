@extends('layouts.app')

@section('title', __('weights.title').' - '.__('common.app_name'))
@section('page_title', __('weights.title'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('weights.title') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('weights.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label">{{ __('common.search') }}</label>
                    <input id="search" type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('weights.search_placeholder') }}">
                </div>
                <div class="col-md-3">
                    <label for="batch_id" class="form-label">{{ __('weights.batch') }}</label>
                    <select id="batch_id" name="batch_id" class="form-select">
                        <option value="">{{ __('weights.all_batches') }}</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}" @selected((int) $batchId === $batch->id)>{{ $batch->batch_no }} - {{ $batch->batch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">{{ __('weights.date_from') }}</label>
                    <input id="date_from" type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">{{ __('weights.date_to') }}</label>
                    <input id="date_to" type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>
                <div class="col-md-2 d-flex justify-content-end">
                    <button type="submit" class="btn btn-outline-success">{{ __('common.filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        @can('weights.create')
            <a href="{{ route('weights.create') }}" class="btn btn-success">{{ __('weights.add_record') }}</a>
        @endcan
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('weights.record_date') }}</th>
                        <th>{{ __('weights.batch') }}</th>
                        <th>{{ __('weights.age_days') }}</th>
                        <th>{{ __('weights.sample_birds') }}</th>
                        <th>{{ __('weights.average_weight') }}</th>
                        <th>{{ __('weights.target_weight') }}</th>
                        <th>{{ __('weights.uniformity_percentage') }}</th>
                        <th>{{ __('weights.weighed_by') }}</th>
                        <th class="text-end">{{ __('weights.actions') }}</th>
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
                            <td>{{ number_format($record->age_days) }}</td>
                            <td>{{ number_format($record->sample_birds) }}</td>
                            <td>{{ number_format((float) $record->average_weight, 3) }}</td>
                            <td>{{ $record->target_weight === null ? __('common.not_set') : number_format((float) $record->target_weight, 3) }}</td>
                            <td>{{ $record->uniformity_percentage === null ? __('common.not_set') : number_format((float) $record->uniformity_percentage, 2).'%' }}</td>
                            <td>{{ $record->weighed_by ?: __('common.not_set') }}</td>
                            <td class="text-end">
                                <a href="{{ route('weights.show', $record) }}" class="btn btn-sm btn-outline-secondary">{{ __('common.view') }}</a>
                                @can('weights.update')
                                    <a href="{{ route('weights.edit', $record) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">{{ __('weights.no_records_found') }}</td>
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
