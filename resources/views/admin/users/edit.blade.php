@extends('layouts.app')

@section('title', __('users.edit_user').' - '.__('common.app_name'))
@section('page_title', __('users.edit_user'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('users.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.edit') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @method('PUT')
                @include('admin.users._form', ['submit' => __('users.update_user')])
            </form>
        </div>
    </div>
@endsection
