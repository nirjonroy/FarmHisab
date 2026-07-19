@extends('layouts.app')

@section('title', __('products.title').' - '.__('common.app_name'))
@section('page_title', __('products.title'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('products.title') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <form method="GET" action="{{ route('products.index') }}" class="row g-2 flex-grow-1">
                    <div class="col-md-3">
                        <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('products.search_placeholder') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="parent_id" class="form-select">
                            <option value="">{{ __('products.all_parent_categories') }}</option>
                            @foreach ($parentCategories as $parentCategory)
                                <option value="{{ $parentCategory->id }}" @selected((int) $parentId === $parentCategory->id)>{{ $parentCategory->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="farm_category_id" class="form-select">
                            <option value="">{{ __('products.all_categories') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) $categoryId === $category->id)>
                                    {{ $category->parent?->display_name }} - {{ $category->display_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="measurement_unit_id" class="form-select">
                            <option value="">{{ __('products.all_units') }}</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" @selected((int) $unitId === $unit->id)>{{ $unit->display_name }} ({{ $unit->display_short_name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="usage_type" class="form-select">
                            <option value="">{{ __('products.all_usage_types') }}</option>
                            @foreach ($usageTypes as $value => $label)
                                <option value="{{ $value }}" @selected($usageType === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">{{ __('products.all_statuses') }}</option>
                            <option value="active" @selected($status === 'active')>{{ __('common.active') }}</option>
                            <option value="inactive" @selected($status === 'inactive')>{{ __('common.inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="stock_tracked" class="form-select">
                            <option value="">{{ __('products.all_stock_tracking') }}</option>
                            <option value="tracked" @selected($stockTracked === 'tracked')>{{ __('products.stock_tracked') }}</option>
                            <option value="not-tracked" @selected($stockTracked === 'not-tracked')>{{ __('products.not_stock_tracked') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-success w-100">{{ __('common.filter') }}</button>
                    </div>
                </form>
                @can('products.manage')
                    <a href="{{ route('products.create') }}" class="btn btn-success">{{ __('products.add_product') }}</a>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('products.product_name') }}</th>
                            <th>{{ __('products.sku') }}</th>
                            <th>{{ __('products.category') }}</th>
                            <th>{{ __('products.measurement_unit') }}</th>
                            <th>{{ __('products.usage_type') }}</th>
                            <th>{{ __('products.stock_tracked') }}</th>
                            <th>{{ __('products.status') }}</th>
                            <th>{{ __('products.created_by') }}</th>
                            <th class="text-end">{{ __('products.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            @php
                                $alternateName = app()->getLocale() === 'bn' ? $product->name_en : $product->name_bn;
                                $showAlternateName = $alternateName && $alternateName !== $product->display_name;
                            @endphp
                            <tr>
                                <td>
                                    {{ $product->display_name }}
                                    @if ($showAlternateName)
                                        <div class="small text-muted">{{ $alternateName }}</div>
                                    @endif
                                </td>
                                <td><span class="fw-semibold">{{ $product->sku }}</span></td>
                                <td>{{ $product->category?->parent?->display_name }} - {{ $product->category?->display_name }}</td>
                                <td>{{ $product->unit?->display_name }} ({{ $product->unit?->display_short_name }})</td>
                                <td><span class="badge text-bg-info">{{ $product->usage_type->label() }}</span></td>
                                <td>
                                    <span class="badge {{ $product->is_stock_tracked ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $product->is_stock_tracked ? __('products.stock_tracked') : __('products.not_stock_tracked') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $product->is_active ? 'text-bg-success' : 'text-bg-danger' }}">
                                        {{ $product->is_active ? __('common.active') : __('common.inactive') }}
                                    </span>
                                </td>
                                <td>{{ $product->createdBy?->name ?: __('common.system') }}</td>
                                <td class="text-end">
                                    @can('products.manage')
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">{{ __('products.no_products_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $products->links() }}
        </div>
    </div>
@endsection
