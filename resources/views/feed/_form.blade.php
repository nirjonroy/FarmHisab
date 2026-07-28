@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="batch_id" class="form-label">{{ __('feed.batch') }}</label>
        <select id="batch_id" name="batch_id" class="form-select @error('batch_id') is-invalid @enderror" required>
            <option value="">{{ __('feed.batch') }}</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->id }}" @selected((int) old('batch_id', $record->batch_id ?? request('batch_id')) === $batch->id)>{{ $batch->batch_no }} - {{ $batch->batch_name }}</option>
            @endforeach
        </select>
        @error('batch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="record_date" class="form-label">{{ __('feed.record_date') }}</label>
        <input id="record_date" type="date" name="record_date" value="{{ old('record_date', isset($record) ? $record->record_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" class="form-control @error('record_date') is-invalid @enderror" required>
        @error('record_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="product_id" class="form-label">{{ __('feed.product') }}</label>
        <select id="product_id" name="product_id" class="form-select @error('product_id') is-invalid @enderror">
            <option value="">{{ __('common.not_set') }}</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected((int) old('product_id', $record->product_id ?? '') === $product->id)>{{ $product->display_name }} ({{ $product->sku }})</option>
            @endforeach
        </select>
        @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="feed_name" class="form-label">{{ __('feed.feed_name') }}</label>
        <input id="feed_name" type="text" name="feed_name" value="{{ old('feed_name', $record->feed_name ?? '') }}" class="form-control @error('feed_name') is-invalid @enderror" maxlength="150">
        @error('feed_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="supplier_name" class="form-label">{{ __('feed.supplier_name') }}</label>
        <input id="supplier_name" type="text" name="supplier_name" value="{{ old('supplier_name', $record->supplier_name ?? '') }}" class="form-control @error('supplier_name') is-invalid @enderror" maxlength="150">
        @error('supplier_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="bags" class="form-label">{{ __('feed.bags') }}</label>
        <input id="bags" type="number" name="bags" min="0.01" step="0.01" value="{{ old('bags', $record->bags ?? '') }}" class="form-control @error('bags') is-invalid @enderror" required>
        @error('bags')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="weight_per_bag" class="form-label">{{ __('feed.weight_per_bag') }}</label>
        <input id="weight_per_bag" type="number" name="weight_per_bag" min="0.01" step="0.01" value="{{ old('weight_per_bag', $record->weight_per_bag ?? 50) }}" class="form-control @error('weight_per_bag') is-invalid @enderror" required>
        @error('weight_per_bag')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="unit_price_per_bag" class="form-label">{{ __('feed.unit_price_per_bag') }}</label>
        <input id="unit_price_per_bag" type="number" name="unit_price_per_bag" min="0" step="0.01" value="{{ old('unit_price_per_bag', $record->unit_price_per_bag ?? 0) }}" class="form-control @error('unit_price_per_bag') is-invalid @enderror" required>
        @error('unit_price_per_bag')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="quantity_kg" class="form-label">{{ __('feed.quantity_kg') }}</label>
        <input id="quantity_kg" type="number" value="{{ old('quantity_kg', $record->quantity_kg ?? '') }}" class="form-control" readonly>
    </div>

    <div class="col-md-3">
        <label for="total_cost" class="form-label">{{ __('feed.total_cost') }}</label>
        <input id="total_cost" type="number" value="{{ old('total_cost', $record->total_cost ?? '') }}" class="form-control" readonly>
    </div>

    <div class="col-12">
        <label for="notes" class="form-label">{{ __('feed.notes') }}</label>
        <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $record->notes ?? '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ isset($record) ? route('feed.show', $record) : route('feed.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
</div>

@push('scripts')
    <script>
        (() => {
            const bags = document.getElementById('bags');
            const weight = document.getElementById('weight_per_bag');
            const price = document.getElementById('unit_price_per_bag');
            const quantity = document.getElementById('quantity_kg');
            const total = document.getElementById('total_cost');

            const calculate = () => {
                const bagCount = parseFloat(bags.value) || 0;
                quantity.value = (bagCount * (parseFloat(weight.value) || 0)).toFixed(2);
                total.value = (bagCount * (parseFloat(price.value) || 0)).toFixed(2);
            };

            [bags, weight, price].forEach((field) => field.addEventListener('input', calculate));
            calculate();
        })();
    </script>
@endpush
