@extends('layouts.app')

@section('title', __('expenses.add_record').' - '.__('common.app_name'))
@section('page_title', __('expenses.add_record'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">{{ __('expenses.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('common.create') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('expenses.store') }}" novalidate>
                @include('expenses._form', ['submit' => __('common.save')])
            </form>
        </div>
    </div>
@endsection
