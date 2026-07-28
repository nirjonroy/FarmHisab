@extends('layouts.app')

@section('title', __('inventory.add_record').' - '.__('common.app_name'))
@section('page_title', __('inventory.add_record'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">{{ __('inventory.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.create') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('inventory.store') }}" novalidate>
                @include('inventory._form', ['submit' => __('common.save')])
            </form>
        </div>
    </div>
@endsection
