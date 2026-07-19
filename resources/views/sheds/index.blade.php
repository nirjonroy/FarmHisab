@extends('layouts.app')

@section('title', 'Sheds - FarmHisab')
@section('page_title', 'Sheds')
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Sheds</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <form method="GET" action="{{ route('sheds.index') }}" class="row g-2 flex-grow-1">
                    <div class="col-md-4">
                        <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Search sheds or farms">
                    </div>
                    <div class="col-md-3">
                        <select name="farm_id" class="form-select">
                            <option value="">All farms</option>
                            @foreach ($farms as $farm)
                                <option value="{{ $farm->id }}" @selected((int) $farmId === $farm->id)>{{ $farm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
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
                @can('farms.manage')
                    <a href="{{ route('sheds.create') }}" class="btn btn-success">Add Shed</a>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Shed name</th>
                            <th>Shed code</th>
                            <th>Farm</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th>Created by</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sheds as $shed)
                            <tr>
                                <td>{{ $shed->name }}</td>
                                <td><span class="fw-semibold">{{ $shed->code }}</span></td>
                                <td>{{ $shed->farm?->name ?: 'Not set' }}</td>
                                <td>{{ number_format($shed->capacity) }}</td>
                                <td>
                                    <span class="badge {{ $shed->is_active ? 'text-bg-success' : 'text-bg-danger' }}">
                                        {{ $shed->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $shed->createdBy?->name ?: 'System' }}</td>
                                <td class="text-end">
                                    @can('farms.manage')
                                        <a href="{{ route('sheds.edit', $shed) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No sheds found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $sheds->links() }}
        </div>
    </div>
@endsection
