@extends('layouts.app')

@section('title', __('batches.add_batch').' - '.__('common.app_name'))
@section('page_title', __('batches.add_batch'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('batches.index') }}">{{ __('batches.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('batches.add_batch') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('batches.store') }}">
                @include('batches._form', ['submit' => __('batches.save_batch')])
            </form>
        </div>
    </div>
@endsection
