@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="batch_name" class="form-label">{{ __('batches.batch_name') }}</label>
        <input id="batch_name" type="text" name="batch_name" value="{{ old('batch_name', $batch->batch_name ?? '') }}" class="form-control @error('batch_name') is-invalid @enderror" required maxlength="150">
        @error('batch_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="farm_id" class="form-label">{{ __('batches.farm') }}</label>
        <select id="farm_id" name="farm_id" class="form-select @error('farm_id') is-invalid @enderror">
            <option value="">{{ __('common.not_set') }}</option>
            @foreach ($farms as $farm)
                <option value="{{ $farm->id }}" @selected((int) old('farm_id', $batch->farm_id ?? '') === $farm->id)>{{ $farm->name }}</option>
            @endforeach
        </select>
        @error('farm_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="bird_type_id" class="form-label">{{ __('batches.bird_type') }}</label>
        <select id="bird_type_id" name="bird_type_id" class="form-select @error('bird_type_id') is-invalid @enderror" required>
            <option value="">{{ __('batches.bird_type') }}</option>
            @foreach ($birdTypes as $birdType)
                <option value="{{ $birdType->id }}" @selected((int) old('bird_type_id', $batch->bird_type_id ?? '') === $birdType->id)>
                    {{ $birdType->parent?->display_name }} - {{ $birdType->display_name }} @if (! $birdType->is_active) ({{ __('common.inactive') }}) @endif
                </option>
            @endforeach
        </select>
        @error('bird_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="breed_id" class="form-label">{{ __('batches.breed') }}</label>
        <select id="breed_id" name="breed_id" class="form-select @error('breed_id') is-invalid @enderror" required>
            <option value="">{{ __('batches.breed') }}</option>
            @foreach ($breeds as $breed)
                <option value="{{ $breed->id }}" data-bird-type-id="{{ $breed->farm_category_id }}" @selected((int) old('breed_id', $batch->breed_id ?? '') === $breed->id)>
                    {{ $breed->category?->display_name }} - {{ $breed->display_name }} @if (! $breed->is_active) ({{ __('common.inactive') }}) @endif
                </option>
            @endforeach
        </select>
        @error('breed_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="supplier_name" class="form-label">{{ __('batches.supplier_name') }}</label>
        <input id="supplier_name" type="text" name="supplier_name" value="{{ old('supplier_name', $batch->supplier_name ?? '') }}" class="form-control @error('supplier_name') is-invalid @enderror" maxlength="150">
        @error('supplier_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="purchase_date" class="form-label">{{ __('batches.purchase_date') }}</label>
        <input id="purchase_date" type="date" name="purchase_date" value="{{ old('purchase_date', isset($batch) ? $batch->purchase_date?->format('Y-m-d') : '') }}" class="form-control @error('purchase_date') is-invalid @enderror" required>
        @error('purchase_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="arrival_date" class="form-label">{{ __('batches.arrival_date') }}</label>
        <input id="arrival_date" type="date" name="arrival_date" value="{{ old('arrival_date', isset($batch) ? $batch->arrival_date?->format('Y-m-d') : '') }}" class="form-control @error('arrival_date') is-invalid @enderror">
        @error('arrival_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="initial_birds" class="form-label">{{ __('batches.initial_birds') }}</label>
        <input id="initial_birds" type="number" name="initial_birds" min="1" step="1" value="{{ old('initial_birds', $batch->initial_birds ?? '') }}" class="form-control @error('initial_birds') is-invalid @enderror" required>
        @error('initial_birds')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="purchase_price_per_bird" class="form-label">{{ __('batches.purchase_price_per_bird') }}</label>
        <input id="purchase_price_per_bird" type="number" name="purchase_price_per_bird" min="0" step="0.01" value="{{ old('purchase_price_per_bird', $batch->purchase_price_per_bird ?? '') }}" class="form-control @error('purchase_price_per_bird') is-invalid @enderror" required>
        @error('purchase_price_per_bird')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="total_purchase_cost" class="form-label">{{ __('batches.total_purchase_cost') }}</label>
        <input id="total_purchase_cost" type="number" name="total_purchase_cost" min="0" step="0.01" value="{{ old('total_purchase_cost', $batch->total_purchase_cost ?? '') }}" class="form-control @error('total_purchase_cost') is-invalid @enderror" readonly required>
        @error('total_purchase_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="expected_market_weight" class="form-label">{{ __('batches.expected_market_weight') }}</label>
        <input id="expected_market_weight" type="number" name="expected_market_weight" min="0" step="0.001" value="{{ old('expected_market_weight', $batch->expected_market_weight ?? '') }}" class="form-control @error('expected_market_weight') is-invalid @enderror">
        @error('expected_market_weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="expected_market_age" class="form-label">{{ __('batches.expected_market_age') }}</label>
        <input id="expected_market_age" type="number" name="expected_market_age" min="0" step="1" value="{{ old('expected_market_age', $batch->expected_market_age ?? '') }}" class="form-control @error('expected_market_age') is-invalid @enderror">
        @error('expected_market_age')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="feed_target_bags" class="form-label">{{ __('batches.feed_target_bags') }}</label>
        <input id="feed_target_bags" type="number" name="feed_target_bags" min="0" step="0.01" value="{{ old('feed_target_bags', $batch->feed_target_bags ?? '') }}" class="form-control @error('feed_target_bags') is-invalid @enderror">
        @error('feed_target_bags')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="status" class="form-label">{{ __('batches.status') }}</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $batch->status->value ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="medicine_budget" class="form-label">{{ __('batches.medicine_budget') }}</label>
        <input id="medicine_budget" type="number" name="medicine_budget" min="0" step="0.01" value="{{ old('medicine_budget', $batch->medicine_budget ?? 0) }}" class="form-control @error('medicine_budget') is-invalid @enderror">
        @error('medicine_budget')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="other_budget" class="form-label">{{ __('batches.other_budget') }}</label>
        <input id="other_budget" type="number" name="other_budget" min="0" step="0.01" value="{{ old('other_budget', $batch->other_budget ?? 0) }}" class="form-control @error('other_budget') is-invalid @enderror">
        @error('other_budget')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="notes" class="form-label">{{ __('batches.notes') }}</label>
        <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $batch->notes ?? '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ isset($batch) ? route('batches.show', $batch) : route('batches.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
</div>

@push('scripts')
    <script>
        (() => {
            const birds = document.getElementById('initial_birds');
            const price = document.getElementById('purchase_price_per_bird');
            const total = document.getElementById('total_purchase_cost');
            const birdType = document.getElementById('bird_type_id');
            const breed = document.getElementById('breed_id');

            const calculateTotal = () => {
                total.value = (((parseFloat(birds.value) || 0) * (parseFloat(price.value) || 0)).toFixed(2));
            };

            const filterBreeds = () => {
                const selected = birdType.value;
                Array.from(breed.options).forEach((option) => {
                    if (! option.value) {
                        option.hidden = false;
                        return;
                    }

                    option.hidden = selected && option.dataset.birdTypeId !== selected;
                });

                if (breed.selectedOptions[0]?.hidden) {
                    breed.value = '';
                }
            };

            birds.addEventListener('input', calculateTotal);
            price.addEventListener('input', calculateTotal);
            birdType.addEventListener('change', filterBreeds);
            calculateTotal();
            filterBreeds();
        })();
    </script>
@endpush
