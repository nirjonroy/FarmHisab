@extends('layouts.app')

@section('title', __('mortality.record_details').' - '.__('common.app_name'))
@section('page_title', __('mortality.record_details'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('mortality.index') }}">{{ __('mortality.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $record->record_date->format('Y-m-d') }}</li>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
        @can('mortality.update')
            <a href="{{ route('mortality.edit', $record) }}" class="btn btn-outline-primary">{{ __('common.edit') }}</a>
            <form method="POST" action="{{ route('mortality.destroy', $record) }}" onsubmit="return confirm('{{ __('mortality.delete_confirm') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">{{ __('common.delete') }}</button>
            </form>
        @endcan
    </div>

    <div class="row g-3 mb-3">
        @foreach ([
            'birds' => number_format($record->birds),
            'type' => $record->type->label(),
            'record_date' => $record->record_date->format('Y-m-d'),
        ] as $label => $value)
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __("mortality.{$label}") }}</div>
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
                    'cause' => $record->cause ?: __('common.not_set'),
                    'action_taken' => $record->action_taken ?: __('common.not_set'),
                    'reported_by' => $record->reported_by ?: __('common.not_set'),
                    'created_by' => $record->createdBy?->name ?: __('common.system'),
                ] as $label => $value)
                    <div class="col-md-4">
                        <div class="text-muted small">{{ __("mortality.{$label}") }}</div>
                        <div class="fw-semibold">{{ $value }}</div>
                    </div>
                @endforeach
                @if ($record->notes)
                    <div class="col-12">
                        <div class="text-muted small">{{ __('mortality.notes') }}</div>
                        <div>{{ $record->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
