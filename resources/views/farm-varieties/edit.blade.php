@extends('layouts.app')

@section('title', __('farm_varieties.edit_variety').' - '.__('common.app_name'))
@section('page_title', __('farm_varieties.edit_variety'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('farm-varieties.index') }}">{{ __('farm_varieties.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.edit') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('farm-varieties.update', $farmVariety) }}">
                @method('PUT')
                @include('farm-varieties._form', ['submit' => __('farm_varieties.update_variety')])
            </form>
        </div>
    </div>
@endsection
