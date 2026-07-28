@extends('layouts.app')

@section('title', __('weights.add_record').' - '.__('common.app_name'))
@section('page_title', __('weights.add_record'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('weights.index') }}">{{ __('weights.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.create') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('weights.store') }}" novalidate>
                @include('weights._form', ['submit' => __('common.save')])
            </form>
        </div>
    </div>
@endsection
