@extends('layouts.app')

@section('title', __('daily_records.record_details').' - '.__('common.app_name'))
@section('page_title', __('daily_records.record_details'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('daily-records.index') }}">{{ __('daily_records.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $record->record_date->format('Y-m-d') }}</li>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
        @can('daily-records.update')
            <a href="{{ route('daily-records.edit', $record) }}" class="btn btn-outline-primary">{{ __('common.edit') }}</a>
            <form method="POST" action="{{ route('daily-records.destroy', $record) }}" onsubmit="return confirm('{{ __('daily_records.delete_confirm') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">{{ __('common.delete') }}</button>
            </form>
        @endcan
    </div>

    <div class="row g-3 mb-3">
        @foreach ([
            'opening_birds' => number_format($record->opening_birds),
            'mortality_birds' => number_format($record->mortality_birds),
            'culled_birds' => number_format($record->culled_birds),
            'sold_birds' => number_format($record->sold_birds),
            'closing_birds' => number_format($record->closing_birds),
            'feed_consumed_bags' => number_format((float) $record->feed_consumed_bags, 2),
            'feed_cost' => 'Tk'.number_format((float) $record->feed_cost, 2),
            'medicine_cost' => 'Tk'.number_format((float) $record->medicine_cost, 2),
        ] as $label => $value)
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __("daily_records.{$label}") }}</div>
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
                    'average_weight' => $record->average_weight ? number_format((float) $record->average_weight, 3) : __('common.not_set'),
                    'temperature' => $record->temperature ? number_format((float) $record->temperature, 2) : __('common.not_set'),
                    'humidity' => $record->humidity ? number_format((float) $record->humidity, 2) : __('common.not_set'),
                    'created_by' => $record->createdBy?->name ?: __('common.system'),
                ] as $label => $value)
                    <div class="col-md-4">
                        <div class="text-muted small">{{ __("daily_records.{$label}") }}</div>
                        <div class="fw-semibold">{{ $value }}</div>
                    </div>
                @endforeach
                @if ($record->notes)
                    <div class="col-12">
                        <div class="text-muted small">{{ __('daily_records.notes') }}</div>
                        <div>{{ $record->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
