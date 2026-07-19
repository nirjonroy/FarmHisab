@extends('layouts.app')

@section('title', __('farms.edit_farm').' - '.__('common.app_name'))
@section('page_title', __('farms.edit_farm'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('farms.index') }}">{{ __('farms.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.edit') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('farms.update', $farm) }}">
                @method('PUT')
                @include('farms._form', ['submit' => __('farms.update_farm')])
            </form>
        </div>
    </div>
@endsection
