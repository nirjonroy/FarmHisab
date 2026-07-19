@extends('layouts.app')

@section('title', $module.' - '.__('common.app_name'))
@section('page_title', $module)
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ $module }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h2 class="h5">{{ __('messages.coming_soon') }}</h2>
            <p class="text-muted mb-0">{{ __('messages.coming_soon_description') }}</p>
        </div>
    </div>
@endsection
