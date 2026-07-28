@extends('layouts.app')

@section('title', __('batches.edit_batch').' - '.__('common.app_name'))
@section('page_title', __('batches.edit_batch'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('batches.index') }}">{{ __('batches.title') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('batches.show', $batch) }}">{{ $batch->batch_no }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('batches.edit_batch') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('batches.update', $batch) }}">
                @method('PUT')
                @include('batches._form', ['submit' => __('batches.update_batch')])
            </form>
        </div>
    </div>
@endsection
