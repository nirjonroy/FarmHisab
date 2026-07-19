@extends('layouts.app')

@section('title', 'Edit Farm Category - FarmHisab')
@section('page_title', 'Edit Farm Category')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('farm-categories.index') }}">Farm Categories</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('farm-categories.update', $farmCategory) }}">
                @method('PUT')
                @include('farm-categories._form', ['submit' => 'Update category'])
            </form>
        </div>
    </div>
@endsection
