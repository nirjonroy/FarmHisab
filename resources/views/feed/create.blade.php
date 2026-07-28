@extends('layouts.app')

@section('title', __('feed.add_record').' - '.__('common.app_name'))
@section('page_title', __('feed.add_record'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('feed.index') }}">{{ __('feed.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('feed.add_record') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('feed.store') }}">
                @include('feed._form', ['submit' => __('feed.save_record')])
            </form>
        </div>
    </div>
@endsection
