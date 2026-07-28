@extends('layouts.app')

@section('title', __('medicine.title').' - '.__('common.app_name'))
@section('page_title', __('medicine.title'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('medicine.title') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('medicine.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label">{{ __('common.search') }}</label>
                    <input id="search" type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('medicine.search_placeholder') }}">
                </div>
                <div class="col-md-3">
                    <label for="batch_id" class="form-label">{{ __('medicine.batch') }}</label>
                    <select id="batch_id" name="batch_id" class="form-select">
                        <option value="">{{ __('medicine.all_batches') }}</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}" @selected((int) $batchId === $batch->id)>{{ $batch->batch_no }} - {{ $batch->batch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label">{{ __('medicine.type') }}</label>
                    <select id="type" name="type" class="form-select">
                        <option value="">{{ __('medicine.all_types') }}</option>
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">{{ __('medicine.date_from') }}</label>
                    <input id="date_from" type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">{{ __('medicine.date_to') }}</label>
                    <input id="date_to" type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-outline-success">{{ __('common.filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        @can('medicine.manage')
            <a href="{{ route('medicine.create') }}" class="btn btn-success">{{ __('medicine.add_record') }}</a>
        @elsecan('vaccinations.manage')
            <a href="{{ route('medicine.create') }}" class="btn btn-success">{{ __('medicine.add_record') }}</a>
        @endcan
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('medicine.record_date') }}</th>
                        <th>{{ __('medicine.batch') }}</th>
                        <th>{{ __('medicine.type') }}</th>
                        <th>{{ __('medicine.medicine_name') }}</th>
                        <th>{{ __('medicine.dosage') }}</th>
                        <th>{{ __('medicine.quantity') }}</th>
                        <th>{{ __('medicine.total_cost') }}</th>
                        <th class="text-end">{{ __('medicine.actions') }}</th>
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
                            <td><span class="badge text-bg-info">{{ $record->type->label() }}</span></td>
                            <td>{{ $record->product?->display_name ?: $record->medicine_name }}</td>
                            <td>{{ $record->dosage ?: __('common.not_set') }}</td>
                            <td>{{ number_format((float) $record->quantity, 2) }} {{ $record->unit }}</td>
                            <td>Tk{{ number_format((float) $record->total_cost, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('medicine.show', $record) }}" class="btn btn-sm btn-outline-secondary">{{ __('common.view') }}</a>
                                @can('medicine.manage')
                                    <a href="{{ route('medicine.edit', $record) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                @elsecan('vaccinations.manage')
                                    <a href="{{ route('medicine.edit', $record) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">{{ __('medicine.no_records_found') }}</td>
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
