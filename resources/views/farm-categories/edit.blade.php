@extends('layouts.app')

@section('title', __('farm_categories.edit_category').' - '.__('common.app_name'))
@section('page_title', __('farm_categories.edit_category'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('farm-categories.index') }}">{{ __('farm_categories.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.edit') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('farm-categories.update', $farmCategory) }}">
                @method('PUT')
                @include('farm-categories._form', ['submit' => __('farm_categories.update_category')])
            </form>
        </div>
    </div>
@endsection
