@extends('layouts.app')

@section('title', 'Edit Farm - FarmHisab')
@section('page_title', 'Edit Farm')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('farms.index') }}">Farms</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('farms.update', $farm) }}">
                @method('PUT')
                @include('farms._form', ['submit' => 'Update farm'])
            </form>
        </div>
    </div>
@endsection
