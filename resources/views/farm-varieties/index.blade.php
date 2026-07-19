@extends('layouts.app')

@section('title', __('farm_varieties.title').' - '.__('common.app_name'))
@section('page_title', __('farm_varieties.title'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('farm_varieties.title') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <form method="GET" action="{{ route('farm-varieties.index') }}" class="row g-2 flex-grow-1">
                    <div class="col-md-3">
                        <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('farm_varieties.search_placeholder') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="parent_id" class="form-select">
                            <option value="">{{ __('farm_varieties.all_parent_categories') }}</option>
                            @foreach ($parentCategories as $parentCategory)
                                <option value="{{ $parentCategory->id }}" @selected((int) $parentId === $parentCategory->id)>{{ $parentCategory->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="farm_category_id" class="form-select">
                            <option value="">{{ __('farm_varieties.all_categories') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) $categoryId === $category->id)>
                                    {{ $category->parent?->display_name }} — {{ $category->display_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">{{ __('common.all_statuses') }}</option>
                            <option value="active" @selected($status === 'active')>{{ __('common.active') }}</option>
                            <option value="inactive" @selected($status === 'inactive')>{{ __('common.inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-outline-success w-100">{{ __('common.filter') }}</button>
                    </div>
                </form>
                @can('farm-varieties.manage')
                    <a href="{{ route('farm-varieties.create') }}" class="btn btn-success">{{ __('farm_varieties.add_variety') }}</a>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('farm_varieties.title') }}</th>
                            <th>{{ __('farm_varieties.category') }}</th>
                            <th>{{ __('farm_varieties.parent_category') }}</th>
                            <th>{{ __('farm_varieties.code') }}</th>
                            <th>{{ __('farm_varieties.slug') }}</th>
                            <th>{{ __('common.status') }}</th>
                            <th>{{ __('farm_varieties.created_by') }}</th>
                            <th class="text-end">{{ __('farm_varieties.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($varieties as $variety)
                            @php
                                $alternateName = app()->getLocale() === 'bn' ? $variety->name_en : $variety->name_bn;
                                $showAlternateName = $alternateName && $alternateName !== $variety->display_name;
                            @endphp
                            <tr>
                                <td>
                                    {{ $variety->display_name }}
                                    @if ($showAlternateName)
                                        <div class="small text-muted">{{ $alternateName }}</div>
                                    @endif
                                </td>
                                <td>{{ $variety->category?->display_name }}</td>
                                <td>{{ $variety->category?->parent?->display_name ?: __('farm_categories.no_parent') }}</td>
                                <td>{{ $variety->code ?: __('common.not_set') }}</td>
                                <td><span class="fw-semibold">{{ $variety->slug }}</span></td>
                                <td>
                                    <span class="badge {{ $variety->is_active ? 'text-bg-success' : 'text-bg-danger' }}">
                                        {{ $variety->is_active ? __('common.active') : __('common.inactive') }}
                                    </span>
                                </td>
                                <td>{{ $variety->createdBy?->name ?: __('common.system') }}</td>
                                <td class="text-end">
                                    @can('farm-varieties.manage')
                                        <a href="{{ route('farm-varieties.edit', $variety) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">{{ __('farm_varieties.no_varieties_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $varieties->links() }}
        </div>
    </div>
@endsection
