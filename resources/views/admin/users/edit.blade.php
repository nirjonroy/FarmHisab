@extends('layouts.app')

@section('title', 'Edit User - FarmHisab')
@section('page_title', 'Edit User')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @method('PUT')
                @include('admin.users._form', ['submit' => 'Update user'])
            </form>
        </div>
    </div>
@endsection
