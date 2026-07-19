@extends('layouts.app')

@section('title', 'Farms - FarmHisab')
@section('page_title', 'Farms')
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Farms</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <form method="GET" action="{{ route('farms.index') }}" class="d-flex gap-2">
                    <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Search farms">
                    <button type="submit" class="btn btn-outline-success">Search</button>
                </form>
                @can('farms.manage')
                    <a href="{{ route('farms.create') }}" class="btn btn-success">Add Farm</a>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Location</th>
                            <th>Sheds</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Created by</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($farms as $farm)
                            <tr>
                                <td>{{ $farm->name }}</td>
                                <td><span class="fw-semibold">{{ $farm->code }}</span></td>
                                <td>
                                    {{ collect([$farm->union_name, $farm->upazila, $farm->district])->filter()->join(', ') ?: 'Not set' }}
                                </td>
                                <td>{{ $farm->sheds_count }}</td>
                                <td>{{ $farm->phone ?: 'Not set' }}</td>
                                <td>
                                    <span class="badge {{ $farm->is_active ? 'text-bg-success' : 'text-bg-danger' }}">
                                        {{ $farm->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $farm->createdBy?->name ?: 'System' }}</td>
                                <td class="text-end">
                                    @can('farms.manage')
                                        <a href="{{ route('farms.edit', $farm) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No farms found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $farms->links() }}
        </div>
    </div>
@endsection
