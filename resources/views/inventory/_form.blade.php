@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="product_id" class="form-label">{{ __('inventory.product') }}</label>
        <select id="product_id" name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
            <option value="">{{ __('inventory.product') }}</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected((int) old('product_id', $record->product_id ?? request('product_id')) === $product->id)>{{ $product->display_name }} - {{ $product->sku }} ({{ $product->unit?->display_short_name }})</option>
            @endforeach
        </select>
        @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="movement_date" class="form-label">{{ __('inventory.movement_date') }}</label>
        <input id="movement_date" type="date" name="movement_date" value="{{ old('movement_date', isset($record) ? $record->movement_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" class="form-control @error('movement_date') is-invalid @enderror" required>
        @error('movement_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="type" class="form-label">{{ __('inventory.type') }}</label>
        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
            @foreach ($types as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $record->type->value ?? 'purchase') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="batch_id" class="form-label">{{ __('inventory.batch') }}</label>
        <select id="batch_id" name="batch_id" class="form-select @error('batch_id') is-invalid @enderror">
            <option value="">{{ __('common.not_set') }}</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->id }}" @selected((int) old('batch_id', $record->batch_id ?? request('batch_id')) === $batch->id)>{{ $batch->batch_no }} - {{ $batch->batch_name }}</option>
            @endforeach
        </select>
        @error('batch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-2">
        <label for="quantity" class="form-label">{{ __('inventory.quantity') }}</label>
        <input id="quantity" type="number" name="quantity" min="0.001" max="999999999" step="0.001" value="{{ old('quantity', $record->quantity ?? '') }}" class="form-control @error('quantity') is-invalid @enderror" required>
        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-2">
        <label for="unit_cost" class="form-label">{{ __('inventory.unit_cost') }}</label>
        <input id="unit_cost" type="number" name="unit_cost" min="0" max="999999999" step="0.01" value="{{ old('unit_cost', $record->unit_cost ?? 0) }}" class="form-control @error('unit_cost') is-invalid @enderror">
        @error('unit_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-2">
        <label for="total_cost" class="form-label">{{ __('inventory.total_cost') }}</label>
        <input id="total_cost" type="number" name="total_cost" min="0" max="999999999" step="0.01" value="{{ old('total_cost', $record->total_cost ?? 0) }}" class="form-control @error('total_cost') is-invalid @enderror">
        @error('total_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="supplier_name" class="form-label">{{ __('inventory.supplier_name') }}</label>
        <input id="supplier_name" type="text" name="supplier_name" value="{{ old('supplier_name', $record->supplier_name ?? '') }}" class="form-control @error('supplier_name') is-invalid @enderror" maxlength="150">
        @error('supplier_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="reference_no" class="form-label">{{ __('inventory.reference_no') }}</label>
        <input id="reference_no" type="text" name="reference_no" value="{{ old('reference_no', $record->reference_no ?? '') }}" class="form-control @error('reference_no') is-invalid @enderror" maxlength="100">
        @error('reference_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="notes" class="form-label">{{ __('inventory.notes') }}</label>
        <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $record->notes ?? '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ isset($record) ? route('inventory.show', $record) : route('inventory.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const quantity = document.getElementById('quantity');
            const unitCost = document.getElementById('unit_cost');
            const totalCost = document.getElementById('total_cost');

            const updateTotal = () => {
                const qty = Number(quantity.value);
                const cost = Number(unitCost.value);

                if (qty > 0 && cost >= 0 && !totalCost.dataset.manual) {
                    totalCost.value = (qty * cost).toFixed(2);
                }
            };

            totalCost.addEventListener('input', () => {
                totalCost.dataset.manual = totalCost.value ? '1' : '';
            });
            quantity.addEventListener('input', updateTotal);
            unitCost.addEventListener('input', updateTotal);
        });
    </script>
@endpush
