@extends('layouts.app')

@section('title', __('weights.record_details').' - '.__('common.app_name'))
@section('page_title', __('weights.record_details'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('weights.index') }}">{{ __('weights.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $record->record_date->format('Y-m-d') }}</li>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
        @can('weights.update')
            <a href="{{ route('weights.edit', $record) }}" class="btn btn-outline-primary">{{ __('common.edit') }}</a>
            <form method="POST" action="{{ route('weights.destroy', $record) }}" onsubmit="return confirm('{{ __('weights.delete_confirm') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">{{ __('common.delete') }}</button>
            </form>
        @endcan
    </div>

    <div class="row g-3 mb-3">
        @foreach ([
            'average_weight' => number_format((float) $record->average_weight, 3).' kg',
            'target_weight' => $record->target_weight === null ? __('common.not_set') : number_format((float) $record->target_weight, 3).' kg',
            'sample_birds' => number_format($record->sample_birds),
            'uniformity_percentage' => $record->uniformity_percentage === null ? __('common.not_set') : number_format((float) $record->uniformity_percentage, 2).'%',
        ] as $label => $value)
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __("weights.{$label}") }}</div>
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
                    'record_date' => $record->record_date->format('Y-m-d'),
                    'age_days' => number_format($record->age_days),
                    'total_weight' => number_format((float) $record->total_weight, 3).' kg',
                    'weighed_by' => $record->weighed_by ?: __('common.not_set'),
                    'created_by' => $record->createdBy?->name ?: __('common.system'),
                ] as $label => $value)
                    <div class="col-md-4">
                        <div class="text-muted small">{{ __("weights.{$label}") }}</div>
                        <div class="fw-semibold">{{ $value }}</div>
                    </div>
                @endforeach
                @if ($record->notes)
                    <div class="col-12">
                        <div class="text-muted small">{{ __('weights.notes') }}</div>
                        <div>{{ $record->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
