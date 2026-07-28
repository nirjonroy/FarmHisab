@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="batch_id" class="form-label">{{ __('mortality.batch') }}</label>
        <select id="batch_id" name="batch_id" class="form-select @error('batch_id') is-invalid @enderror" required>
            <option value="">{{ __('mortality.batch') }}</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->id }}" @selected((int) old('batch_id', $record->batch_id ?? request('batch_id')) === $batch->id)>{{ $batch->batch_no }} - {{ $batch->batch_name }}</option>
            @endforeach
        </select>
        @error('batch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="record_date" class="form-label">{{ __('mortality.record_date') }}</label>
        <input id="record_date" type="date" name="record_date" value="{{ old('record_date', isset($record) ? $record->record_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" class="form-control @error('record_date') is-invalid @enderror" required>
        @error('record_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="type" class="form-label">{{ __('mortality.type') }}</label>
        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
            @foreach ($types as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $record->type->value ?? 'mortality') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="birds" class="form-label">{{ __('mortality.birds') }}</label>
        <input id="birds" type="number" name="birds" min="1" step="1" value="{{ old('birds', $record->birds ?? '') }}" class="form-control @error('birds') is-invalid @enderror" required>
        @error('birds')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-9">
        <label for="cause" class="form-label">{{ __('mortality.cause') }}</label>
        <input id="cause" type="text" name="cause" value="{{ old('cause', $record->cause ?? '') }}" class="form-control @error('cause') is-invalid @enderror" maxlength="150">
        @error('cause')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="action_taken" class="form-label">{{ __('mortality.action_taken') }}</label>
        <input id="action_taken" type="text" name="action_taken" value="{{ old('action_taken', $record->action_taken ?? '') }}" class="form-control @error('action_taken') is-invalid @enderror" maxlength="150">
        @error('action_taken')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="reported_by" class="form-label">{{ __('mortality.reported_by') }}</label>
        <input id="reported_by" type="text" name="reported_by" value="{{ old('reported_by', $record->reported_by ?? '') }}" class="form-control @error('reported_by') is-invalid @enderror" maxlength="150">
        @error('reported_by')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="notes" class="form-label">{{ __('mortality.notes') }}</label>
        <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $record->notes ?? '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ isset($record) ? route('mortality.show', $record) : route('mortality.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
</div>
