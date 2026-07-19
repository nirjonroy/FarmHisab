@extends('layouts.app')

@section('title', __('sheds.edit_shed').' - '.__('common.app_name'))
@section('page_title', __('sheds.edit_shed'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('sheds.index') }}">{{ __('sheds.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.edit') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('sheds.update', $shed) }}">
                @method('PUT')
                @include('sheds._form', ['submit' => __('sheds.update_shed')])
            </form>
        </div>
    </div>
@endsection
