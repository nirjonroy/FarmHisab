@extends('layouts.app')

@section('title', __('feed.edit_record').' - '.__('common.app_name'))
@section('page_title', __('feed.edit_record'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('feed.index') }}">{{ __('feed.title') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('feed.show', $record) }}">{{ $record->record_date->format('Y-m-d') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('feed.edit_record') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('feed.update', $record) }}">
                @method('PUT')
                @include('feed._form', ['submit' => __('feed.update_record')])
            </form>
        </div>
    </div>
@endsection
