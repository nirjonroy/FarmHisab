@extends('layouts.app')

@section('title', __('expenses.title').' - '.__('common.app_name'))
@section('page_title', __('expenses.title'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('expenses.title') }}</li>
@endsection

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">{{ __('expenses.total_filtered') }}</div>
                    <div class="h3 mb-0">Tk{{ number_format((float) $totalAmount, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('expenses.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label">{{ __('common.search') }}</label>
                    <input id="search" type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('expenses.search_placeholder') }}">
                </div>
                <div class="col-md-3">
                    <label for="batch_id" class="form-label">{{ __('expenses.batch') }}</label>
                    <select id="batch_id" name="batch_id" class="form-select">
                        <option value="">{{ __('expenses.all_batches') }}</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}" @selected((int) $batchId === $batch->id)>{{ $batch->batch_no }} - {{ $batch->batch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="category" class="form-label">{{ __('expenses.category') }}</label>
                    <select id="category" name="category" class="form-select">
                        <option value="">{{ __('expenses.all_categories') }}</option>
                        @foreach ($categories as $value => $label)
                            <option value="{{ $value }}" @selected($category === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="payment_method" class="form-label">{{ __('expenses.payment_method') }}</label>
                    <select id="payment_method" name="payment_method" class="form-select">
                        <option value="">{{ __('expenses.all_payment_methods') }}</option>
                        @foreach ($paymentMethods as $value => $label)
                            <option value="{{ $value }}" @selected($paymentMethod === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">{{ __('expenses.date_from') }}</label>
                    <input id="date_from" type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">{{ __('expenses.date_to') }}</label>
                    <input id="date_to" type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-outline-success">{{ __('common.filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        @can('expenses.manage')
            <a href="{{ route('expenses.create') }}" class="btn btn-success">{{ __('expenses.add_record') }}</a>
        @endcan
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('expenses.expense_date') }}</th>
                        <th>{{ __('expenses.batch') }}</th>
                        <th>{{ __('expenses.category') }}</th>
                        <th>{{ __('expenses.expense_title') }}</th>
                        <th>{{ __('expenses.payee') }}</th>
                        <th>{{ __('expenses.payment_method') }}</th>
                        <th class="text-end">{{ __('expenses.amount') }}</th>
                        <th class="text-end">{{ __('expenses.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $record->expense_date->format('Y-m-d') }}</td>
                            <td>
                                @if ($record->batch)
                                    <div class="fw-semibold">{{ $record->batch->batch_no }}</div>
                                    <div class="text-muted small">{{ $record->batch->batch_name }}</div>
                                @else
                                    {{ __('common.not_set') }}
                                @endif
                            </td>
                            <td><span class="badge text-bg-secondary">{{ $record->category->label() }}</span></td>
                            <td>{{ $record->title }}</td>
                            <td>{{ $record->payee ?: __('common.not_set') }}</td>
                            <td>{{ $record->payment_method->label() }}</td>
                            <td class="text-end">Tk{{ number_format((float) $record->amount, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('expenses.show', $record) }}" class="btn btn-sm btn-outline-secondary">{{ __('common.view') }}</a>
                                @can('expenses.manage')
                                    <a href="{{ route('expenses.edit', $record) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">{{ __('expenses.no_records_found') }}</td>
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
