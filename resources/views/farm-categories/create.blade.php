@extends('layouts.app')

@section('title', __('farm_categories.add_category').' - '.__('common.app_name'))
@section('page_title', __('farm_categories.add_category'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('farm-categories.index') }}">{{ __('farm_categories.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.add') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('farm-categories.store') }}">
                @include('farm-categories._form', ['submit' => __('farm_categories.save_category')])
            </form>
        </div>
    </div>
@endsection
