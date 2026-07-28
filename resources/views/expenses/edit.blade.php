@extends('layouts.app')

@section('title', __('expenses.edit_record').' - '.__('common.app_name'))
@section('page_title', __('expenses.edit_record'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">{{ __('expenses.title') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expenses.show', $record) }}">{{ $record->title }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.edit') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('expenses.update', $record) }}" novalidate>
                @method('PUT')
                @include('expenses._form', ['submit' => __('common.save')])
            </form>
        </div>
    </div>
@endsection
