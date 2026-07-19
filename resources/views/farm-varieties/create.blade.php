@extends('layouts.app')

@section('title', __('farm_varieties.add_variety').' - '.__('common.app_name'))
@section('page_title', __('farm_varieties.add_variety'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('farm-varieties.index') }}">{{ __('farm_varieties.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.add') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('farm-varieties.store') }}">
                @include('farm-varieties._form', ['submit' => __('farm_varieties.save_variety')])
            </form>
        </div>
    </div>
@endsection
