@extends('layouts.app')

@section('title', __('dashboard.title').' - '.__('common.app_name'))
@section('page_title', __('dashboard.title'))

@section('content')
    <div class="alert alert-warning" role="alert">
        {{ __('dashboard.placeholder_notice') }}
    </div>
    <div class="row g-3">
        @foreach ($metrics as $label => $value)
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __($label) }}</div>
                        <div class="display-6 fw-semibold">{{ $value }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
