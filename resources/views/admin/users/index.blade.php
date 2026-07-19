@extends('layouts.app')

@section('title', 'Users - FarmHisab')
@section('page_title', 'Users')
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Users</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
                    <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Search name or email">
                    <button type="submit" class="btn btn-outline-success">Search</button>
                </form>
                @can('users.create')
                    <a href="{{ route('admin.users.create') }}" class="btn btn-success">Create user</a>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge text-bg-secondary">{{ ucfirst($user->roles->pluck('name')->first() ?? 'none') }}</span></td>
                                <td>
                                    <span class="badge {{ $user->is_active ? 'text-bg-success' : 'text-bg-danger' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                        @can('users.update')
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        @endcan
                                        @can('users.activate')
                                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-warning">
                                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        @endcan
                                        @can('users.delete')
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUser{{ $user->id }}">
                                                Delete
                                            </button>
                                            <div class="modal fade text-start" id="deleteUser{{ $user->id }}" tabindex="-1" aria-labelledby="deleteUser{{ $user->id }}Label" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteUser{{ $user->id }}Label">Delete user</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Delete {{ $user->name }}? This action cannot be undone.
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}
        </div>
    </div>
@endsection
