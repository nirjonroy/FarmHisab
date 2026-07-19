@extends('layouts.app')

@section('title', $moduleTitle.' - '.__('common.app_name'))
@section('page_title', $moduleTitle)
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ $moduleTitle }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h2 class="h5">{{ __('messages.coming_soon') }}</h2>
            <p class="text-muted mb-1">{{ __('messages.module_planned') }}</p>
            <p class="text-muted">{{ __('messages.module_not_available') }}</p>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-success">{{ __('messages.back_to_dashboard') }}</a>
        </div>
    </div>
@endsection
