@extends('layouts.app')

@section('title', __('sales.title').' - '.__('common.app_name'))
@section('page_title', __('sales.title'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('sales.title') }}</li>
@endsection

@section('content')
    <div class="row g-3 mb-3">
        @foreach ([
            'total_filtered' => 'Tk'.number_format((float) $totalAmount, 2),
            'total_due' => 'Tk'.number_format((float) $totalDue, 2),
            'total_birds' => number_format($totalBirds),
        ] as $label => $value)
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __("sales.{$label}") }}</div>
                        <div class="h3 mb-0">{{ $value }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('sales.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label">{{ __('common.search') }}</label>
                    <input id="search" type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('sales.search_placeholder') }}">
                </div>
                <div class="col-md-3">
                    <label for="batch_id" class="form-label">{{ __('sales.batch') }}</label>
                    <select id="batch_id" name="batch_id" class="form-select">
                        <option value="">{{ __('sales.all_batches') }}</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}" @selected((int) $batchId === $batch->id)>{{ $batch->batch_no }} - {{ $batch->batch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="payment_method" class="form-label">{{ __('sales.payment_method') }}</label>
                    <select id="payment_method" name="payment_method" class="form-select">
                        <option value="">{{ __('sales.all_payment_methods') }}</option>
                        @foreach ($paymentMethods as $value => $label)
                            <option value="{{ $value }}" @selected($paymentMethod === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">{{ __('sales.date_from') }}</label>
                    <input id="date_from" type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">{{ __('sales.date_to') }}</label>
                    <input id="date_to" type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-outline-success">{{ __('common.filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        @can('sales.manage')
            <a href="{{ route('sales.create') }}" class="btn btn-success">{{ __('sales.add_record') }}</a>
        @endcan
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('sales.sale_date') }}</th>
                        <th>{{ __('sales.batch') }}</th>
                        <th>{{ __('sales.buyer_name') }}</th>
                        <th>{{ __('sales.birds_sold') }}</th>
                        <th>{{ __('sales.total_weight') }}</th>
                        <th>{{ __('sales.rate_per_kg') }}</th>
                        <th class="text-end">{{ __('sales.total_amount') }}</th>
                        <th class="text-end">{{ __('sales.due_amount') }}</th>
                        <th class="text-end">{{ __('sales.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $record->sale_date->format('Y-m-d') }}</td>
                            <td>
                                <div class="fw-semibold">{{ $record->batch?->batch_no }}</div>
                                <div class="text-muted small">{{ $record->batch?->batch_name }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $record->buyer_name }}</div>
                                <div class="text-muted small">{{ $record->buyer_phone ?: __('common.not_set') }}</div>
                            </td>
                            <td>{{ number_format($record->birds_sold) }}</td>
                            <td>{{ number_format((float) $record->total_weight, 3) }}</td>
                            <td>Tk{{ number_format((float) $record->rate_per_kg, 2) }}</td>
                            <td class="text-end">Tk{{ number_format((float) $record->total_amount, 2) }}</td>
                            <td class="text-end">Tk{{ number_format((float) $record->due_amount, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('sales.show', $record) }}" class="btn btn-sm btn-outline-secondary">{{ __('common.view') }}</a>
                                @can('sales.manage')
                                    <a href="{{ route('sales.edit', $record) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">{{ __('sales.no_records_found') }}</td>
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
