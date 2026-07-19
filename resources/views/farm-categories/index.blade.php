@extends('layouts.app')

@section('title', 'Farm Categories - FarmHisab')
@section('page_title', 'Farm Categories')
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Farm Categories</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <form method="GET" action="{{ route('farm-categories.index') }}" class="row g-2 flex-grow-1">
                    <div class="col-md-3">
                        <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Search name or slug">
                    </div>
                    <div class="col-md-3">
                        <select name="parent_id" class="form-select">
                            <option value="">All parents</option>
                            @foreach ($parentCategories as $parentCategory)
                                <option value="{{ $parentCategory->id }}" @selected((int) $parentId === $parentCategory->id)>{{ $parentCategory->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="level" class="form-select">
                            <option value="">All levels</option>
                            <option value="top-level" @selected($level === 'top-level')>Top-level</option>
                            <option value="child" @selected($level === 'child')>Child</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All statuses</option>
                            <option value="active" @selected($status === 'active')>Active</option>
                            <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-success w-100">Filter</button>
                    </div>
                </form>
                @can('farm-categories.manage')
                    <a href="{{ route('farm-categories.create') }}" class="btn btn-success">Add Category</a>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Category name</th>
                            <th>Parent category</th>
                            <th>Slug</th>
                            <th>Children</th>
                            <th>Status</th>
                            <th>Created by</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>
                                    @if ($category->parent_id)
                                        <span class="text-muted me-2">-</span>
                                        <span class="badge text-bg-light border me-2">Child</span>
                                    @else
                                        <span class="badge text-bg-success me-2">Top-level</span>
                                    @endif
                                    {{ $category->name }}
                                </td>
                                <td>{{ $category->parent?->name ?: 'No Parent' }}</td>
                                <td><span class="fw-semibold">{{ $category->slug }}</span></td>
                                <td>{{ $category->children_count }}</td>
                                <td>
                                    <span class="badge {{ $category->is_active ? 'text-bg-success' : 'text-bg-danger' }}">
                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $category->createdBy?->name ?: 'System' }}</td>
                                <td class="text-end">
                                    @can('farm-categories.manage')
                                        <a href="{{ route('farm-categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No farm categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $categories->links() }}
        </div>
    </div>
@endsection
