@extends('layouts.app')

@section('title', __('batches.title').' - '.__('common.app_name'))
@section('page_title', __('batches.title'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('batches.title') }}</li>
@endsection

@section('content')
    <div class="row g-3 mb-3">
        @foreach ([
            'active_batches' => $summary['active_batches'],
            'completed_batches' => $summary['completed_batches'],
            'total_birds' => $summary['total_birds'],
            'total_investment' => 'Tk'.number_format($summary['total_investment'], 2),
        ] as $label => $value)
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __("batches.{$label}") }}</div>
                        <div class="h3 mb-0">{{ $value }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <form method="GET" action="{{ route('batches.index') }}" class="row g-2 flex-grow-1">
                    <div class="col-md-3">
                        <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('batches.search_placeholder') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">{{ __('batches.all_statuses') }}</option>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control" aria-label="{{ __('batches.date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control" aria-label="{{ __('batches.date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-success w-100">{{ __('common.filter') }}</button>
                    </div>
                </form>
                @can('batches.manage')
                    <a href="{{ route('batches.create') }}" class="btn btn-success">{{ __('batches.add_batch') }}</a>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('batches.batch_no') }}</th>
                            <th>{{ __('batches.batch_name') }}</th>
                            <th>{{ __('batches.farm') }}</th>
                            <th>{{ __('batches.bird_type') }}</th>
                            <th>{{ __('batches.breed') }}</th>
                            <th>{{ __('batches.initial_birds') }}</th>
                            <th>{{ __('batches.purchase_date') }}</th>
                            <th>{{ __('batches.status') }}</th>
                            <th class="text-end">{{ __('batches.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($batches as $batch)
                            <tr>
                                <td><span class="fw-semibold">{{ $batch->batch_no }}</span></td>
                                <td>{{ $batch->batch_name }}</td>
                                <td>{{ $batch->farm?->name ?: __('common.not_set') }}</td>
                                <td>{{ $batch->birdType?->display_name }}</td>
                                <td>{{ $batch->breed?->display_name }}</td>
                                <td>{{ number_format($batch->initial_birds) }}</td>
                                <td>{{ $batch->purchase_date?->format('Y-m-d') }}</td>
                                <td><span class="badge text-bg-{{ $batch->status->value === 'active' ? 'success' : ($batch->status->value === 'completed' ? 'primary' : 'secondary') }}">{{ $batch->status->label() }}</span></td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('batches.show', $batch) }}" class="btn btn-outline-success">{{ __('common.view') }}</a>
                                        @can('batches.manage')
                                            <a href="{{ route('batches.edit', $batch) }}" class="btn btn-outline-primary">{{ __('common.edit') }}</a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">{{ __('batches.no_batches_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $batches->links() }}
        </div>
    </div>
@endsection
