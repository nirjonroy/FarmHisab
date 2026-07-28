@extends('layouts.app')

@section('title', __('medicine.record_details').' - '.__('common.app_name'))
@section('page_title', __('medicine.record_details'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('medicine.index') }}">{{ __('medicine.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $record->record_date->format('Y-m-d') }}</li>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
        @can('medicine.manage')
            <a href="{{ route('medicine.edit', $record) }}" class="btn btn-outline-primary">{{ __('common.edit') }}</a>
            <form method="POST" action="{{ route('medicine.destroy', $record) }}" onsubmit="return confirm('{{ __('medicine.delete_confirm') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">{{ __('common.delete') }}</button>
            </form>
        @elsecan('vaccinations.manage')
            <a href="{{ route('medicine.edit', $record) }}" class="btn btn-outline-primary">{{ __('common.edit') }}</a>
        @endcan
    </div>

    <div class="row g-3 mb-3">
        @foreach ([
            'quantity' => number_format((float) $record->quantity, 2).' '.$record->unit,
            'unit_price' => 'Tk'.number_format((float) $record->unit_price, 2),
            'total_cost' => 'Tk'.number_format((float) $record->total_cost, 2),
            'next_due_date' => $record->next_due_date?->format('Y-m-d') ?: __('common.not_set'),
        ] as $label => $value)
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __("medicine.{$label}") }}</div>
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
                    'type' => $record->type->label(),
                    'product' => $record->product?->display_name ?: __('common.not_set'),
                    'medicine_name' => $record->medicine_name ?: __('common.not_set'),
                    'supplier_name' => $record->supplier_name ?: __('common.not_set'),
                    'dosage' => $record->dosage ?: __('common.not_set'),
                    'purpose' => $record->purpose ?: __('common.not_set'),
                    'created_by' => $record->createdBy?->name ?: __('common.system'),
                ] as $label => $value)
                    <div class="col-md-4">
                        <div class="text-muted small">{{ __("medicine.{$label}") }}</div>
                        <div class="fw-semibold">{{ $value }}</div>
                    </div>
                @endforeach
                @if ($record->notes)
                    <div class="col-12">
                        <div class="text-muted small">{{ __('medicine.notes') }}</div>
                        <div>{{ $record->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
