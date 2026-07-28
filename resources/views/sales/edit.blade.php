@extends('layouts.app')

@section('title', __('sales.edit_record').' - '.__('common.app_name'))
@section('page_title', __('sales.edit_record'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">{{ __('sales.title') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sales.show', $record) }}">{{ $record->buyer_name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.edit') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('sales.update', $record) }}" novalidate>
                @method('PUT')
                @include('sales._form', ['submit' => __('common.save')])
            </form>
        </div>
    </div>
@endsection
