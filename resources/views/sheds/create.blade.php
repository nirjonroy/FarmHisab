@extends('layouts.app')

@section('title', 'Add Shed - FarmHisab')
@section('page_title', 'Add Shed')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('sheds.index') }}">Sheds</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('sheds.store') }}">
                @include('sheds._form', ['submit' => 'Save shed'])
            </form>
        </div>
    </div>
@endsection
