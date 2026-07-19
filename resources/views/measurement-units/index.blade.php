@extends('layouts.app')

@section('title', __('measurement_units.title').' - '.__('common.app_name'))
@section('page_title', __('measurement_units.title'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('measurement_units.title') }}</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <form method="GET" action="{{ route('measurement-units.index') }}" class="row g-2 flex-grow-1">
                    <div class="col-md-5">
                        <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('measurement_units.search_placeholder') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">{{ __('measurement_units.all_statuses') }}</option>
                            <option value="active" @selected($status === 'active')>{{ __('common.active') }}</option>
                            <option value="inactive" @selected($status === 'inactive')>{{ __('common.inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-success w-100">{{ __('common.filter') }}</button>
                    </div>
                </form>
                @can('measurement-units.manage')
                    <a href="{{ route('measurement-units.create') }}" class="btn btn-success">{{ __('measurement_units.add_unit') }}</a>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('measurement_units.title') }}</th>
                            <th>{{ __('measurement_units.english_short_name') }}</th>
                            <th>{{ __('measurement_units.code') }}</th>
                            <th>{{ __('measurement_units.decimal_places') }}</th>
                            <th>{{ __('measurement_units.status') }}</th>
                            <th>{{ __('measurement_units.created_by') }}</th>
                            <th class="text-end">{{ __('measurement_units.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($units as $unit)
                            @php
                                $alternateName = app()->getLocale() === 'bn' ? $unit->name_en : $unit->name_bn;
                                $showAlternateName = $alternateName && $alternateName !== $unit->display_name;
                            @endphp
                            <tr>
                                <td>
                                    {{ $unit->display_name }}
                                    @if ($showAlternateName)
                                        <div class="small text-muted">{{ $alternateName }}</div>
                                    @endif
                                </td>
                                <td>{{ $unit->display_short_name }}</td>
                                <td><span class="fw-semibold">{{ $unit->code }}</span></td>
                                <td>{{ $unit->decimal_places }}</td>
                                <td>
                                    <span class="badge {{ $unit->is_active ? 'text-bg-success' : 'text-bg-danger' }}">
                                        {{ $unit->is_active ? __('common.active') : __('common.inactive') }}
                                    </span>
                                </td>
                                <td>{{ $unit->createdBy?->name ?: __('common.system') }}</td>
                                <td class="text-end">
                                    @can('measurement-units.manage')
                                        <a href="{{ route('measurement-units.edit', $unit) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">{{ __('measurement_units.no_units_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $units->links() }}
        </div>
    </div>
@endsection
