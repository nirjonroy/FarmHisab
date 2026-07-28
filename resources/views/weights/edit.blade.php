@extends('layouts.app')

@section('title', __('weights.edit_record').' - '.__('common.app_name'))
@section('page_title', __('weights.edit_record'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('weights.index') }}">{{ __('weights.title') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('weights.show', $record) }}">{{ $record->record_date->format('Y-m-d') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.edit') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('weights.update', $record) }}" novalidate>
                @method('PUT')
                @include('weights._form', ['submit' => __('common.save')])
            </form>
        </div>
    </div>
@endsection
