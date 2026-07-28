@extends('layouts.app')

@section('title', __('feed.record_details').' - '.__('common.app_name'))
@section('page_title', __('feed.record_details'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('feed.index') }}">{{ __('feed.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $record->record_date->format('Y-m-d') }}</li>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
        @can('feed.manage')
            <a href="{{ route('feed.edit', $record) }}" class="btn btn-outline-primary">{{ __('common.edit') }}</a>
            <form method="POST" action="{{ route('feed.destroy', $record) }}" onsubmit="return confirm('{{ __('feed.delete_confirm') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">{{ __('common.delete') }}</button>
            </form>
        @endcan
    </div>

    <div class="row g-3 mb-3">
        @foreach ([
            'bags' => number_format((float) $record->bags, 2),
            'weight_per_bag' => number_format((float) $record->weight_per_bag, 2),
            'quantity_kg' => number_format((float) $record->quantity_kg, 2),
            'unit_price_per_bag' => 'Tk'.number_format((float) $record->unit_price_per_bag, 2),
            'total_cost' => 'Tk'.number_format((float) $record->total_cost, 2),
        ] as $label => $value)
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __("feed.{$label}") }}</div>
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
                    'record_date' => $record->record_date->format('Y-m-d'),
                    'batch' => $record->batch?->batch_no.' - '.$record->batch?->batch_name,
                    'product' => $record->product?->display_name ?: __('common.not_set'),
                    'feed_name' => $record->feed_name ?: __('common.not_set'),
                    'supplier_name' => $record->supplier_name ?: __('common.not_set'),
                    'created_by' => $record->createdBy?->name ?: __('common.system'),
                ] as $label => $value)
                    <div class="col-md-4">
                        <div class="text-muted small">{{ __("feed.{$label}") }}</div>
                        <div class="fw-semibold">{{ $value }}</div>
                    </div>
                @endforeach
                @if ($record->notes)
                    <div class="col-12">
                        <div class="text-muted small">{{ __('feed.notes') }}</div>
                        <div>{{ $record->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
