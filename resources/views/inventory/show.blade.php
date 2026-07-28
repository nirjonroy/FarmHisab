@extends('layouts.app')

@section('title', __('inventory.record_details').' - '.__('common.app_name'))
@section('page_title', __('inventory.record_details'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">{{ __('inventory.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $record->product?->display_name }}</li>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
        @can('inventory.manage')
            <a href="{{ route('inventory.edit', $record) }}" class="btn btn-outline-primary">{{ __('common.edit') }}</a>
            <form method="POST" action="{{ route('inventory.destroy', $record) }}" onsubmit="return confirm('{{ __('inventory.delete_confirm') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">{{ __('common.delete') }}</button>
            </form>
        @endcan
    </div>

    <div class="row g-3 mb-3">
        @foreach ([
            'quantity' => number_format((float) $record->quantity, 3).' '.$record->product?->unit?->display_short_name,
            'type' => $record->type->label(),
            'unit_cost' => 'Tk'.number_format((float) $record->unit_cost, 2),
            'total_cost' => 'Tk'.number_format((float) $record->total_cost, 2),
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

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                @foreach ([
                    'product' => $record->product?->display_name.' - '.$record->product?->sku,
                    'batch' => $record->batch ? $record->batch->batch_no.' - '.$record->batch->batch_name : __('common.not_set'),
                    'movement_date' => $record->movement_date->format('Y-m-d'),
                    'supplier_name' => $record->supplier_name ?: __('common.not_set'),
                    'reference_no' => $record->reference_no ?: __('common.not_set'),
                    'created_by' => $record->createdBy?->name ?: __('common.system'),
                ] as $label => $value)
                    <div class="col-md-4">
                        <div class="text-muted small">{{ __("inventory.{$label}") }}</div>
                        <div class="fw-semibold">{{ $value }}</div>
                    </div>
                @endforeach
                @if ($record->notes)
                    <div class="col-12">
                        <div class="text-muted small">{{ __('inventory.notes') }}</div>
                        <div>{{ $record->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
