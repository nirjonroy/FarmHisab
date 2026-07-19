@extends('layouts.app')

@section('title', __('farms.title').' - '.__('common.app_name'))
@section('page_title', __('farms.title'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('farms.title') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <form method="GET" action="{{ route('farms.index') }}" class="d-flex gap-2">
                    <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('farms.search_placeholder') }}">
                    <button type="submit" class="btn btn-outline-success">{{ __('common.search') }}</button>
                </form>
                @can('farms.manage')
                    <a href="{{ route('farms.create') }}" class="btn btn-success">{{ __('farms.add_farm') }}</a>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('farms.farm_name') }}</th>
                            <th>{{ __('farms.code') }}</th>
                            <th>{{ __('farms.location') }}</th>
                            <th>{{ __('farms.shed_count') }}</th>
                            <th>{{ __('farms.phone') }}</th>
                            <th>{{ __('common.status') }}</th>
                            <th>{{ __('farms.created_by') }}</th>
                            <th class="text-end">{{ __('farms.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($farms as $farm)
                            <tr>
                                <td>{{ $farm->name }}</td>
                                <td><span class="fw-semibold">{{ $farm->code }}</span></td>
                                <td>
                                    {{ collect([$farm->union_name, $farm->upazila, $farm->district])->filter()->join(', ') ?: __('common.not_set') }}
                                </td>
                                <td>{{ $farm->sheds_count }}</td>
                                <td>{{ $farm->phone ?: __('common.not_set') }}</td>
                                <td>
                                    <span class="badge {{ $farm->is_active ? 'text-bg-success' : 'text-bg-danger' }}">
                                        {{ $farm->is_active ? __('common.active') : __('common.inactive') }}
                                    </span>
                                </td>
                                <td>{{ $farm->createdBy?->name ?: __('common.system') }}</td>
                                <td class="text-end">
                                    @can('farms.manage')
                                        <a href="{{ route('farms.edit', $farm) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">{{ __('farms.no_farms_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $farms->links() }}
        </div>
    </div>
@endsection
