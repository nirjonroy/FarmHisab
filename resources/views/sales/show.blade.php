@extends('layouts.app')

@section('title', __('sales.record_details').' - '.__('common.app_name'))
@section('page_title', __('sales.record_details'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">{{ __('sales.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $record->buyer_name }}</li>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
        @can('sales.manage')
            <a href="{{ route('sales.edit', $record) }}" class="btn btn-outline-primary">{{ __('common.edit') }}</a>
            <form method="POST" action="{{ route('sales.destroy', $record) }}" onsubmit="return confirm('{{ __('sales.delete_confirm') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">{{ __('common.delete') }}</button>
            </form>
        @endcan
    </div>

    <div class="row g-3 mb-3">
        @foreach ([
            'total_amount' => 'Tk'.number_format((float) $record->total_amount, 2),
            'paid_amount' => 'Tk'.number_format((float) $record->paid_amount, 2),
            'due_amount' => 'Tk'.number_format((float) $record->due_amount, 2),
            'birds_sold' => number_format($record->birds_sold),
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

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                @foreach ([
                    'batch' => $record->batch?->batch_no.' - '.$record->batch?->batch_name,
                    'sale_date' => $record->sale_date->format('Y-m-d'),
                    'buyer_name' => $record->buyer_name,
                    'buyer_phone' => $record->buyer_phone ?: __('common.not_set'),
                    'average_weight' => $record->average_weight === null ? __('common.not_set') : number_format((float) $record->average_weight, 3).' kg',
                    'total_weight' => number_format((float) $record->total_weight, 3).' kg',
                    'rate_per_kg' => 'Tk'.number_format((float) $record->rate_per_kg, 2),
                    'payment_method' => $record->payment_method->label(),
                    'reference_no' => $record->reference_no ?: __('common.not_set'),
                    'created_by' => $record->createdBy?->name ?: __('common.system'),
                ] as $label => $value)
                    <div class="col-md-4">
                        <div class="text-muted small">{{ __("sales.{$label}") }}</div>
                        <div class="fw-semibold">{{ $value }}</div>
                    </div>
                @endforeach
                @if ($record->notes)
                    <div class="col-12">
                        <div class="text-muted small">{{ __('sales.notes') }}</div>
                        <div>{{ $record->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
