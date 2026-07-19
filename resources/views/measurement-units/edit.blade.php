@extends('layouts.app')

@section('title', __('measurement_units.edit_unit').' - '.__('common.app_name'))
@section('page_title', __('measurement_units.edit_unit'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('measurement-units.index') }}">{{ __('measurement_units.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('measurement_units.edit_unit') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('measurement-units.update', $measurementUnit) }}">
                @method('PUT')
                @include('measurement-units._form', ['submit' => __('measurement_units.update_unit')])
            </form>
        </div>
    </div>
@endsection
