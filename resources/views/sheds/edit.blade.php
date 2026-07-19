@extends('layouts.app')

@section('title', 'Edit Shed - FarmHisab')
@section('page_title', 'Edit Shed')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('sheds.index') }}">Sheds</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('sheds.update', $shed) }}">
                @method('PUT')
                @include('sheds._form', ['submit' => 'Update shed'])
            </form>
        </div>
    </div>
@endsection
