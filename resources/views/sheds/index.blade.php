@extends('layouts.app')

@section('title', __('sheds.title').' - '.__('common.app_name'))
@section('page_title', __('sheds.title'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('sheds.title') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <form method="GET" action="{{ route('sheds.index') }}" class="row g-2 flex-grow-1">
                    <div class="col-md-4">
                        <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('sheds.search_placeholder') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="farm_id" class="form-select">
                            <option value="">{{ __('sheds.filter_by_farm') }}</option>
                            @foreach ($farms as $farm)
                                <option value="{{ $farm->id }}" @selected((int) $farmId === $farm->id)>{{ $farm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">{{ __('sheds.filter_by_status') }}</option>
                            <option value="active" @selected($status === 'active')>{{ __('common.active') }}</option>
                            <option value="inactive" @selected($status === 'inactive')>{{ __('common.inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-success w-100">{{ __('common.filter') }}</button>
                    </div>
                </form>
                @can('farms.manage')
                    <a href="{{ route('sheds.create') }}" class="btn btn-success">{{ __('sheds.add_shed') }}</a>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('sheds.shed_name') }}</th>
                            <th>{{ __('sheds.shed_code') }}</th>
                            <th>{{ __('sheds.farm') }}</th>
                            <th>{{ __('sheds.capacity') }}</th>
                            <th>{{ __('common.status') }}</th>
                            <th>{{ __('sheds.created_by') }}</th>
                            <th class="text-end">{{ __('sheds.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sheds as $shed)
                            <tr>
                                <td>{{ $shed->name }}</td>
                                <td><span class="fw-semibold">{{ $shed->code }}</span></td>
                                <td>{{ $shed->farm?->name ?: __('common.not_set') }}</td>
                                <td>{{ number_format($shed->capacity) }}</td>
                                <td>
                                    <span class="badge {{ $shed->is_active ? 'text-bg-success' : 'text-bg-danger' }}">
                                        {{ $shed->is_active ? __('common.active') : __('common.inactive') }}
                                    </span>
                                </td>
                                <td>{{ $shed->createdBy?->name ?: __('common.system') }}</td>
                                <td class="text-end">
                                    @can('farms.manage')
                                        <a href="{{ route('sheds.edit', $shed) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">{{ __('sheds.no_sheds_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $sheds->links() }}
        </div>
    </div>
@endsection
