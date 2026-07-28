@extends('layouts.app')

@section('title', __('inventory.title').' - '.__('common.app_name'))
@section('page_title', __('inventory.title'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('inventory.title') }}</li>
@endsection

@section('content')
    @if ($currentStock !== null)
        <div class="row g-3 mb-3">
            @foreach ([
                'current_stock' => number_format((float) $currentStock, 3),
                'stock_value' => 'Tk'.number_format((float) $stockValue, 2),
            ] as $label => $value)
                <div class="col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small">{{ __("inventory.{$label}") }}</div>
                            <div class="h3 mb-0">{{ $value }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('inventory.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label">{{ __('common.search') }}</label>
                    <input id="search" type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('inventory.search_placeholder') }}">
                </div>
                <div class="col-md-3">
                    <label for="product_id" class="form-label">{{ __('inventory.product') }}</label>
                    <select id="product_id" name="product_id" class="form-select">
                        <option value="">{{ __('inventory.all_products') }}</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected((int) $productId === $product->id)>{{ $product->display_name }} - {{ $product->sku }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="batch_id" class="form-label">{{ __('inventory.batch') }}</label>
                    <select id="batch_id" name="batch_id" class="form-select">
                        <option value="">{{ __('inventory.all_batches') }}</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}" @selected((int) $batchId === $batch->id)>{{ $batch->batch_no }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label">{{ __('inventory.type') }}</label>
                    <select id="type" name="type" class="form-select">
                        <option value="">{{ __('inventory.all_types') }}</option>
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">{{ __('inventory.date_from') }}</label>
                    <input id="date_from" type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">{{ __('inventory.date_to') }}</label>
                    <input id="date_to" type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-outline-success">{{ __('common.filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        @can('inventory.manage')
            <a href="{{ route('inventory.create') }}" class="btn btn-success">{{ __('inventory.add_record') }}</a>
        @endcan
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('inventory.movement_date') }}</th>
                        <th>{{ __('inventory.product') }}</th>
                        <th>{{ __('inventory.type') }}</th>
                        <th>{{ __('inventory.batch') }}</th>
                        <th>{{ __('inventory.supplier_name') }}</th>
                        <th class="text-end">{{ __('inventory.quantity') }}</th>
                        <th class="text-end">{{ __('inventory.total_cost') }}</th>
                        <th class="text-end">{{ __('inventory.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $record->movement_date->format('Y-m-d') }}</td>
                            <td>
                                <div class="fw-semibold">{{ $record->product?->display_name }}</div>
                                <div class="text-muted small">{{ $record->product?->sku }}</div>
                            </td>
                            <td><span class="badge {{ $record->type->direction() === 'in' ? 'text-bg-success' : 'text-bg-warning' }}">{{ $record->type->label() }}</span></td>
                            <td>{{ $record->batch?->batch_no ?: __('common.not_set') }}</td>
                            <td>{{ $record->supplier_name ?: __('common.not_set') }}</td>
                            <td class="text-end">{{ number_format((float) $record->quantity, 3) }} {{ $record->product?->unit?->display_short_name }}</td>
                            <td class="text-end">Tk{{ number_format((float) $record->total_cost, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('inventory.show', $record) }}" class="btn btn-sm btn-outline-secondary">{{ __('common.view') }}</a>
                                @can('inventory.manage')
                                    <a href="{{ route('inventory.edit', $record) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">{{ __('inventory.no_records_found') }}</td>
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
