@extends('layouts.app')

@section('title', __('daily_records.add_record').' - '.__('common.app_name'))
@section('page_title', __('daily_records.add_record'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('daily-records.index') }}">{{ __('daily_records.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('daily_records.add_record') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('daily-records.store') }}">
                @include('daily-records._form', ['submit' => __('daily_records.save_record')])
            </form>
        </div>
    </div>
@endsection
