@extends('layouts.app')

@section('title', 'Dashboard - FarmHisab')
@section('page_title', 'Dashboard')

@section('content')
    <div class="alert alert-warning" role="alert">
        Dashboard figures are temporary placeholders for Step 2. No poultry business records have been inserted.
    </div>
    <div class="row g-3">
        @foreach ($metrics as $label => $value)
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ $label }}</div>
                        <div class="display-6 fw-semibold">{{ $value }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
