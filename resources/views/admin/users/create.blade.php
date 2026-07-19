@extends('layouts.app')

@section('title', __('users.create_user').' - '.__('common.app_name'))
@section('page_title', __('users.create_user'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('users.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.create') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @include('admin.users._form', ['submit' => __('users.add_user')])
            </form>
        </div>
    </div>
@endsection
