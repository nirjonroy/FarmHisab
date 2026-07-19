@extends('layouts.app')

@section('title', __('products.edit_product').' - '.__('common.app_name'))
@section('page_title', __('products.edit_product'))
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('products.title') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('products.edit_product') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('products.update', $product) }}">
                @method('PUT')
                @include('products._form', ['submit' => __('products.update_product')])
            </form>
        </div>
    </div>
@endsection
