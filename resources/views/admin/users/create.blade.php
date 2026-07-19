@extends('layouts.app')

@section('title', 'Create User - FarmHisab')
@section('page_title', 'Create User')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @include('admin.users._form', ['submit' => 'Create user'])
            </form>
        </div>
    </div>
@endsection
