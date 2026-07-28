@extends('layouts.app')

@section('title', __('medicine.edit_record').' - '.__('common.app_name'))
@section('page_title', __('medicine.edit_record'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('medicine.index') }}">{{ __('medicine.title') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('medicine.show', $record) }}">{{ $record->record_date->format('Y-m-d') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('medicine.edit_record') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('medicine.update', $record) }}">
                @method('PUT')
                @include('medicine._form', ['submit' => __('medicine.update_record')])
            </form>
        </div>
    </div>
@endsection
