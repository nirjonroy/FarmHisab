@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="batch_id" class="form-label">{{ __('weights.batch') }}</label>
        <select id="batch_id" name="batch_id" class="form-select @error('batch_id') is-invalid @enderror" required>
            <option value="">{{ __('weights.batch') }}</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->id }}" @selected((int) old('batch_id', $record->batch_id ?? request('batch_id')) === $batch->id)>{{ $batch->batch_no }} - {{ $batch->batch_name }}</option>
            @endforeach
        </select>
        @error('batch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="record_date" class="form-label">{{ __('weights.record_date') }}</label>
        <input id="record_date" type="date" name="record_date" value="{{ old('record_date', isset($record) ? $record->record_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" class="form-control @error('record_date') is-invalid @enderror" required>
        @error('record_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="age_days" class="form-label">{{ __('weights.age_days') }}</label>
        <input id="age_days" type="number" name="age_days" min="0" max="1000" step="1" value="{{ old('age_days', $record->age_days ?? '') }}" class="form-control @error('age_days') is-invalid @enderror">
        @error('age_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="sample_birds" class="form-label">{{ __('weights.sample_birds') }}</label>
        <input id="sample_birds" type="number" name="sample_birds" min="1" step="1" value="{{ old('sample_birds', $record->sample_birds ?? '') }}" class="form-control @error('sample_birds') is-invalid @enderror" required>
        @error('sample_birds')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="average_weight" class="form-label">{{ __('weights.average_weight') }}</label>
        <input id="average_weight" type="number" name="average_weight" min="0.001" max="99999" step="0.001" value="{{ old('average_weight', $record->average_weight ?? '') }}" class="form-control @error('average_weight') is-invalid @enderror" required>
        @error('average_weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="total_weight" class="form-label">{{ __('weights.total_weight') }}</label>
        <input id="total_weight" type="number" name="total_weight" min="0.001" max="9999999" step="0.001" value="{{ old('total_weight', $record->total_weight ?? '') }}" class="form-control @error('total_weight') is-invalid @enderror">
        @error('total_weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="target_weight" class="form-label">{{ __('weights.target_weight') }}</label>
        <input id="target_weight" type="number" name="target_weight" min="0.001" max="99999" step="0.001" value="{{ old('target_weight', $record->target_weight ?? '') }}" class="form-control @error('target_weight') is-invalid @enderror">
        @error('target_weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="uniformity_percentage" class="form-label">{{ __('weights.uniformity_percentage') }}</label>
        <input id="uniformity_percentage" type="number" name="uniformity_percentage" min="0" max="100" step="0.01" value="{{ old('uniformity_percentage', $record->uniformity_percentage ?? '') }}" class="form-control @error('uniformity_percentage') is-invalid @enderror">
        @error('uniformity_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-9">
        <label for="weighed_by" class="form-label">{{ __('weights.weighed_by') }}</label>
        <input id="weighed_by" type="text" name="weighed_by" value="{{ old('weighed_by', $record->weighed_by ?? '') }}" class="form-control @error('weighed_by') is-invalid @enderror" maxlength="150">
        @error('weighed_by')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="notes" class="form-label">{{ __('weights.notes') }}</label>
        <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $record->notes ?? '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ isset($record) ? route('weights.show', $record) : route('weights.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sampleBirds = document.getElementById('sample_birds');
            const averageWeight = document.getElementById('average_weight');
            const totalWeight = document.getElementById('total_weight');

            const updateTotal = () => {
                const birds = Number(sampleBirds.value);
                const average = Number(averageWeight.value);

                if (birds > 0 && average > 0 && !totalWeight.dataset.manual) {
                    totalWeight.value = (birds * average).toFixed(3);
                }
            };

            totalWeight.addEventListener('input', () => {
                totalWeight.dataset.manual = totalWeight.value ? '1' : '';
            });
            sampleBirds.addEventListener('input', updateTotal);
            averageWeight.addEventListener('input', updateTotal);
        });
    </script>
@endpush
