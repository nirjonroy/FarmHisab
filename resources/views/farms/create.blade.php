@extends('layouts.app')

@section('title', 'Add Farm - FarmHisab')
@section('page_title', 'Add Farm')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('farms.index') }}">Farms</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('farms.store') }}">
                @include('farms._form', ['submit' => 'Save farm'])
            </form>
        </div>
    </div>
@endsection
