@extends('layouts.app')

@section('title', __('expenses.record_details').' - '.__('common.app_name'))
@section('page_title', __('expenses.record_details'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">{{ __('expenses.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $record->title }}</li>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
        @can('expenses.manage')
            <a href="{{ route('expenses.edit', $record) }}" class="btn btn-outline-primary">{{ __('common.edit') }}</a>
            <form method="POST" action="{{ route('expenses.destroy', $record) }}" onsubmit="return confirm('{{ __('expenses.delete_confirm') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">{{ __('common.delete') }}</button>
            </form>
        @endcan
    </div>

    <div class="row g-3 mb-3">
        @foreach ([
            'amount' => 'Tk'.number_format((float) $record->amount, 2),
            'category' => $record->category->label(),
            'payment_method' => $record->payment_method->label(),
            'expense_date' => $record->expense_date->format('Y-m-d'),
        ] as $label => $value)
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __("expenses.{$label}") }}</div>
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
                    'expense_title' => $record->title,
                    'batch' => $record->batch ? $record->batch->batch_no.' - '.$record->batch->batch_name : __('common.not_set'),
                    'payee' => $record->payee ?: __('common.not_set'),
                    'reference_no' => $record->reference_no ?: __('common.not_set'),
                    'created_by' => $record->createdBy?->name ?: __('common.system'),
                ] as $label => $value)
                    <div class="col-md-4">
                        <div class="text-muted small">{{ __("expenses.{$label}") }}</div>
                        <div class="fw-semibold">{{ $value }}</div>
                    </div>
                @endforeach
                @if ($record->notes)
                    <div class="col-12">
                        <div class="text-muted small">{{ __('expenses.notes') }}</div>
                        <div>{{ $record->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
