@extends('layouts.app')

@section('title', 'Add Farm Category - FarmHisab')
@section('page_title', 'Add Farm Category')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('farm-categories.index') }}">Farm Categories</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('farm-categories.store') }}">
                @include('farm-categories._form', ['submit' => 'Save category'])
            </form>
        </div>
    </div>
@endsection
