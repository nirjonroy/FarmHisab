@extends('layouts.app')

@section('title', $batch->batch_no.' - '.__('common.app_name'))
@section('page_title', __('batches.batch_details'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('batches.index') }}">{{ __('batches.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $batch->batch_no }}</li>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
        @can('batches.manage')
            <a href="{{ route('batches.edit', $batch) }}" class="btn btn-outline-primary">{{ __('common.edit') }}</a>
            <form method="POST" action="{{ route('batches.destroy', $batch) }}" onsubmit="return confirm('{{ __('batches.delete_confirm') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">{{ __('common.delete') }}</button>
            </form>
        @endcan
    </div>

    <div class="row g-3 mb-3">
        @foreach ([
            'current_birds' => number_format($details['current_birds']),
            'dead_birds' => number_format($details['dead_birds']),
            'sold_birds' => number_format($details['sold_birds']),
            'remaining_birds' => number_format($details['remaining_birds']),
            'feed_consumed' => number_format($details['feed_consumed'], 2),
            'medicine_cost' => 'Tk'.number_format($details['medicine_cost'], 2),
            'investment' => 'Tk'.number_format($details['investment'], 2),
            'profit_loss' => 'Tk'.number_format($details['profit'], 2),
        ] as $label => $value)
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __("batches.{$label}") }}</div>
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
                    'batch_no' => $batch->batch_no,
                    'batch_name' => $batch->batch_name,
                    'farm' => $batch->farm?->name ?: __('common.not_set'),
                    'bird_type' => $batch->birdType?->display_name,
                    'breed' => $batch->breed?->display_name,
                    'supplier_name' => $batch->supplier_name ?: __('common.not_set'),
                    'purchase_date' => $batch->purchase_date?->format('Y-m-d'),
                    'arrival_date' => $batch->arrival_date?->format('Y-m-d') ?: __('common.not_set'),
                    'initial_birds' => number_format($batch->initial_birds),
                    'purchase_price_per_bird' => 'Tk'.number_format((float) $batch->purchase_price_per_bird, 2),
                    'total_purchase_cost' => 'Tk'.number_format((float) $batch->total_purchase_cost, 2),
                    'status' => $batch->status->label(),
                ] as $label => $value)
                    <div class="col-md-4">
                        <div class="text-muted small">{{ __("batches.{$label}") }}</div>
                        <div class="fw-semibold">{{ $value }}</div>
                    </div>
                @endforeach
                @if ($batch->notes)
                    <div class="col-12">
                        <div class="text-muted small">{{ __('batches.notes') }}</div>
                        <div>{{ $batch->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
