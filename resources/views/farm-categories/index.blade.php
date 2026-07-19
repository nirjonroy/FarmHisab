@extends('layouts.app')

@section('title', __('farm_categories.title').' - '.__('common.app_name'))
@section('page_title', __('farm_categories.title'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('farm_categories.title') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <form method="GET" action="{{ route('farm-categories.index') }}" class="row g-2 flex-grow-1">
                    <div class="col-md-3">
                        <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('farm_categories.search_placeholder') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="parent_id" class="form-select">
                            <option value="">{{ __('farm_categories.filter_by_parent') }}</option>
                            @foreach ($parentCategories as $parentCategory)
                                <option value="{{ $parentCategory->id }}" @selected((int) $parentId === $parentCategory->id)>{{ $parentCategory->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="level" class="form-select">
                            <option value="">{{ __('farm_categories.filter_by_level') }}</option>
                            <option value="top-level" @selected($level === 'top-level')>{{ __('farm_categories.top_level') }}</option>
                            <option value="child" @selected($level === 'child')>{{ __('farm_categories.child_category') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">{{ __('farm_categories.filter_by_status') }}</option>
                            <option value="active" @selected($status === 'active')>{{ __('common.active') }}</option>
                            <option value="inactive" @selected($status === 'inactive')>{{ __('common.inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-success w-100">{{ __('common.filter') }}</button>
                    </div>
                </form>
                @can('farm-categories.manage')
                    <a href="{{ route('farm-categories.create') }}" class="btn btn-success">{{ __('farm_categories.add_category') }}</a>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('farm_categories.category_name') }}</th>
                            <th>{{ __('farm_categories.parent_category') }}</th>
                            <th>{{ __('farm_categories.slug') }}</th>
                            <th>{{ __('farm_categories.child_count') }}</th>
                            <th>{{ __('common.status') }}</th>
                            <th>{{ __('farm_categories.created_by') }}</th>
                            <th class="text-end">{{ __('farm_categories.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            @php
                                $alternateName = app()->getLocale() === 'bn' ? $category->name_en : $category->name_bn;
                                $showAlternateName = $alternateName && $alternateName !== $category->display_name;
                            @endphp
                            <tr>
                                <td>
                                    @if ($category->parent_id)
                                        <span class="text-muted me-2">-</span>
                                        <span class="badge text-bg-light border me-2">{{ __('farm_categories.child_category') }}</span>
                                    @else
                                        <span class="badge text-bg-success me-2">{{ __('farm_categories.top_level') }}</span>
                                    @endif
                                    {{ $category->display_name }}
                                    @if ($showAlternateName)
                                        <div class="small text-muted ms-4">{{ $alternateName }}</div>
                                    @endif
                                </td>
                                <td>{{ $category->parent?->display_name ?: __('farm_categories.no_parent') }}</td>
                                <td><span class="fw-semibold">{{ $category->slug }}</span></td>
                                <td>{{ $category->children_count }}</td>
                                <td>
                                    <span class="badge {{ $category->is_active ? 'text-bg-success' : 'text-bg-danger' }}">
                                        {{ $category->is_active ? __('common.active') : __('common.inactive') }}
                                    </span>
                                </td>
                                <td>{{ $category->createdBy?->name ?: __('common.system') }}</td>
                                <td class="text-end">
                                    @can('farm-categories.manage')
                                        <a href="{{ route('farm-categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">{{ __('farm_categories.no_categories_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $categories->links() }}
        </div>
    </div>
@endsection
