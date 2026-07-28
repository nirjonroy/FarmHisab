@extends('layouts.app')

@section('title', __('inventory.edit_record').' - '.__('common.app_name'))
@section('page_title', __('inventory.edit_record'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">{{ __('inventory.title') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('inventory.show', $record) }}">{{ $record->product?->display_name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.edit') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('inventory.update', $record) }}" novalidate>
                @method('PUT')
                @include('inventory._form', ['submit' => __('common.save')])
            </form>
        </div>
    </div>
@endsection
