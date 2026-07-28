@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="batch_id" class="form-label">{{ __('medicine.batch') }}</label>
        <select id="batch_id" name="batch_id" class="form-select @error('batch_id') is-invalid @enderror" required>
            <option value="">{{ __('medicine.batch') }}</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->id }}" @selected((int) old('batch_id', $record->batch_id ?? request('batch_id')) === $batch->id)>{{ $batch->batch_no }} - {{ $batch->batch_name }}</option>
            @endforeach
        </select>
        @error('batch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="record_date" class="form-label">{{ __('medicine.record_date') }}</label>
        <input id="record_date" type="date" name="record_date" value="{{ old('record_date', isset($record) ? $record->record_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" class="form-control @error('record_date') is-invalid @enderror" required>
        @error('record_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="type" class="form-label">{{ __('medicine.type') }}</label>
        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
            @foreach ($types as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $record->type->value ?? 'medicine') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="product_id" class="form-label">{{ __('medicine.product') }}</label>
        <select id="product_id" name="product_id" class="form-select @error('product_id') is-invalid @enderror">
            <option value="">{{ __('common.not_set') }}</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected((int) old('product_id', $record->product_id ?? '') === $product->id)>{{ $product->display_name }} ({{ $product->sku }})</option>
            @endforeach
        </select>
        @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="medicine_name" class="form-label">{{ __('medicine.medicine_name') }}</label>
        <input id="medicine_name" type="text" name="medicine_name" value="{{ old('medicine_name', $record->medicine_name ?? '') }}" class="form-control @error('medicine_name') is-invalid @enderror" maxlength="150">
        @error('medicine_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="supplier_name" class="form-label">{{ __('medicine.supplier_name') }}</label>
        <input id="supplier_name" type="text" name="supplier_name" value="{{ old('supplier_name', $record->supplier_name ?? '') }}" class="form-control @error('supplier_name') is-invalid @enderror" maxlength="150">
        @error('supplier_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="dosage" class="form-label">{{ __('medicine.dosage') }}</label>
        <input id="dosage" type="text" name="dosage" value="{{ old('dosage', $record->dosage ?? '') }}" class="form-control @error('dosage') is-invalid @enderror" maxlength="150">
        @error('dosage')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="purpose" class="form-label">{{ __('medicine.purpose') }}</label>
        <input id="purpose" type="text" name="purpose" value="{{ old('purpose', $record->purpose ?? '') }}" class="form-control @error('purpose') is-invalid @enderror" maxlength="150">
        @error('purpose')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="quantity" class="form-label">{{ __('medicine.quantity') }}</label>
        <input id="quantity" type="number" name="quantity" min="0.01" step="0.01" value="{{ old('quantity', $record->quantity ?? '') }}" class="form-control @error('quantity') is-invalid @enderror" required>
        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="unit" class="form-label">{{ __('medicine.unit') }}</label>
        <input id="unit" type="text" name="unit" value="{{ old('unit', $record->unit ?? '') }}" class="form-control @error('unit') is-invalid @enderror" maxlength="50">
        @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="unit_price" class="form-label">{{ __('medicine.unit_price') }}</label>
        <input id="unit_price" type="number" name="unit_price" min="0" step="0.01" value="{{ old('unit_price', $record->unit_price ?? 0) }}" class="form-control @error('unit_price') is-invalid @enderror" required>
        @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="total_cost" class="form-label">{{ __('medicine.total_cost') }}</label>
        <input id="total_cost" type="number" value="{{ old('total_cost', $record->total_cost ?? '') }}" class="form-control" readonly>
    </div>

    <div class="col-md-6">
        <label for="next_due_date" class="form-label">{{ __('medicine.next_due_date') }}</label>
        <input id="next_due_date" type="date" name="next_due_date" value="{{ old('next_due_date', isset($record) ? $record->next_due_date?->format('Y-m-d') : '') }}" class="form-control @error('next_due_date') is-invalid @enderror">
        @error('next_due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="notes" class="form-label">{{ __('medicine.notes') }}</label>
        <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $record->notes ?? '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ isset($record) ? route('medicine.show', $record) : route('medicine.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
</div>

@push('scripts')
    <script>
        (() => {
            const quantity = document.getElementById('quantity');
            const price = document.getElementById('unit_price');
            const total = document.getElementById('total_cost');
            const calculate = () => total.value = ((parseFloat(quantity.value) || 0) * (parseFloat(price.value) || 0)).toFixed(2);
            [quantity, price].forEach((field) => field.addEventListener('input', calculate));
            calculate();
        })();
    </script>
@endpush
