@extends('layouts.app')

@section('title', __('sheds.add_shed').' - '.__('common.app_name'))
@section('page_title', __('sheds.add_shed'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('sheds.index') }}">{{ __('sheds.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.add') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('sheds.store') }}">
                @include('sheds._form', ['submit' => __('sheds.save_shed')])
            </form>
        </div>
    </div>
@endsection
