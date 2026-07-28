@extends('layouts.app')

@section('title', __('mortality.edit_record').' - '.__('common.app_name'))
@section('page_title', __('mortality.edit_record'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('mortality.index') }}">{{ __('mortality.title') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('mortality.show', $record) }}">{{ $record->record_date->format('Y-m-d') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('mortality.edit_record') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('mortality.update', $record) }}">
                @method('PUT')
                @include('mortality._form', ['submit' => __('mortality.update_record')])
            </form>
        </div>
    </div>
@endsection
