@extends('layouts.app')

@section('title', __('mortality.add_record').' - '.__('common.app_name'))
@section('page_title', __('mortality.add_record'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('mortality.index') }}">{{ __('mortality.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('mortality.add_record') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('mortality.store') }}">
                @include('mortality._form', ['submit' => __('mortality.save_record')])
            </form>
        </div>
    </div>
@endsection
