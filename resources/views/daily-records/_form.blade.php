@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="batch_id" class="form-label">{{ __('daily_records.batch') }}</label>
        <select id="batch_id" name="batch_id" class="form-select @error('batch_id') is-invalid @enderror" required>
            <option value="">{{ __('daily_records.batch') }}</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->id }}" data-opening-birds="{{ $batch->dailyRecords()->ordered()->value('closing_birds') ?? $batch->initial_birds }}" @selected((int) old('batch_id', $record->batch_id ?? request('batch_id')) === $batch->id)>
                    {{ $batch->batch_no }} - {{ $batch->batch_name }}
                </option>
            @endforeach
        </select>
        @error('batch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="record_date" class="form-label">{{ __('daily_records.record_date') }}</label>
        <input id="record_date" type="date" name="record_date" value="{{ old('record_date', isset($record) ? $record->record_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" class="form-control @error('record_date') is-invalid @enderror" required>
        @error('record_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="opening_birds" class="form-label">{{ __('daily_records.opening_birds') }}</label>
        <input id="opening_birds" type="number" name="opening_birds" min="0" step="1" value="{{ old('opening_birds', $record->opening_birds ?? $defaultOpeningBirds ?? '') }}" class="form-control @error('opening_birds') is-invalid @enderror" required>
        @error('opening_birds')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="mortality_birds" class="form-label">{{ __('daily_records.mortality_birds') }}</label>
        <input id="mortality_birds" type="number" name="mortality_birds" min="0" step="1" value="{{ old('mortality_birds', $record->mortality_birds ?? 0) }}" class="form-control @error('mortality_birds') is-invalid @enderror">
        @error('mortality_birds')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="culled_birds" class="form-label">{{ __('daily_records.culled_birds') }}</label>
        <input id="culled_birds" type="number" name="culled_birds" min="0" step="1" value="{{ old('culled_birds', $record->culled_birds ?? 0) }}" class="form-control @error('culled_birds') is-invalid @enderror">
        @error('culled_birds')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="sold_birds" class="form-label">{{ __('daily_records.sold_birds') }}</label>
        <input id="sold_birds" type="number" name="sold_birds" min="0" step="1" value="{{ old('sold_birds', $record->sold_birds ?? 0) }}" class="form-control @error('sold_birds') is-invalid @enderror">
        @error('sold_birds')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="closing_birds" class="form-label">{{ __('daily_records.closing_birds') }}</label>
        <input id="closing_birds" type="number" value="{{ old('closing_birds', $record->closing_birds ?? '') }}" class="form-control" readonly>
    </div>

    <div class="col-md-3">
        <label for="feed_consumed_bags" class="form-label">{{ __('daily_records.feed_consumed_bags') }}</label>
        <input id="feed_consumed_bags" type="number" name="feed_consumed_bags" min="0" step="0.01" value="{{ old('feed_consumed_bags', $record->feed_consumed_bags ?? 0) }}" class="form-control @error('feed_consumed_bags') is-invalid @enderror">
        @error('feed_consumed_bags')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="feed_cost" class="form-label">{{ __('daily_records.feed_cost') }}</label>
        <input id="feed_cost" type="number" name="feed_cost" min="0" step="0.01" value="{{ old('feed_cost', $record->feed_cost ?? 0) }}" class="form-control @error('feed_cost') is-invalid @enderror">
        @error('feed_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="medicine_cost" class="form-label">{{ __('daily_records.medicine_cost') }}</label>
        <input id="medicine_cost" type="number" name="medicine_cost" min="0" step="0.01" value="{{ old('medicine_cost', $record->medicine_cost ?? 0) }}" class="form-control @error('medicine_cost') is-invalid @enderror">
        @error('medicine_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="average_weight" class="form-label">{{ __('daily_records.average_weight') }}</label>
        <input id="average_weight" type="number" name="average_weight" min="0" step="0.001" value="{{ old('average_weight', $record->average_weight ?? '') }}" class="form-control @error('average_weight') is-invalid @enderror">
        @error('average_weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="temperature" class="form-label">{{ __('daily_records.temperature') }}</label>
        <input id="temperature" type="number" name="temperature" step="0.01" value="{{ old('temperature', $record->temperature ?? '') }}" class="form-control @error('temperature') is-invalid @enderror">
        @error('temperature')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="humidity" class="form-label">{{ __('daily_records.humidity') }}</label>
        <input id="humidity" type="number" name="humidity" min="0" max="100" step="0.01" value="{{ old('humidity', $record->humidity ?? '') }}" class="form-control @error('humidity') is-invalid @enderror">
        @error('humidity')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="notes" class="form-label">{{ __('daily_records.notes') }}</label>
        <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $record->notes ?? '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ isset($record) ? route('daily-records.show', $record) : route('daily-records.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
</div>

@push('scripts')
    <script>
        (() => {
            const batch = document.getElementById('batch_id');
            const opening = document.getElementById('opening_birds');
            const mortality = document.getElementById('mortality_birds');
            const culled = document.getElementById('culled_birds');
            const sold = document.getElementById('sold_birds');
            const closing = document.getElementById('closing_birds');

            const calculate = () => {
                const value = Math.max(0, (parseInt(opening.value, 10) || 0) - (parseInt(mortality.value, 10) || 0) - (parseInt(culled.value, 10) || 0) - (parseInt(sold.value, 10) || 0));
                closing.value = value;
            };

            batch.addEventListener('change', () => {
                const selected = batch.selectedOptions[0];
                if (selected?.dataset.openingBirds && ! opening.value) {
                    opening.value = selected.dataset.openingBirds;
                }
                calculate();
            });

            [opening, mortality, culled, sold].forEach((field) => field.addEventListener('input', calculate));
            calculate();
        })();
    </script>
@endpush
