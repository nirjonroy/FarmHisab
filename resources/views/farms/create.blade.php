@extends('layouts.app')

@section('title', __('farms.add_farm').' - '.__('common.app_name'))
@section('page_title', __('farms.add_farm'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('farms.index') }}">{{ __('farms.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.add') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('farms.store') }}">
                @include('farms._form', ['submit' => __('farms.save_farm')])
            </form>
        </div>
    </div>
@endsection
