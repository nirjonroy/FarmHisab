@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="batch_id" class="form-label">{{ __('sales.batch') }}</label>
        <select id="batch_id" name="batch_id" class="form-select @error('batch_id') is-invalid @enderror" required>
            <option value="">{{ __('sales.batch') }}</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->id }}" @selected((int) old('batch_id', $record->batch_id ?? request('batch_id')) === $batch->id)>{{ $batch->batch_no }} - {{ $batch->batch_name }}</option>
            @endforeach
        </select>
        @error('batch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="sale_date" class="form-label">{{ __('sales.sale_date') }}</label>
        <input id="sale_date" type="date" name="sale_date" value="{{ old('sale_date', isset($record) ? $record->sale_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" class="form-control @error('sale_date') is-invalid @enderror" required>
        @error('sale_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="payment_method" class="form-label">{{ __('sales.payment_method') }}</label>
        <select id="payment_method" name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
            @foreach ($paymentMethods as $value => $label)
                <option value="{{ $value }}" @selected(old('payment_method', $record->payment_method->value ?? 'cash') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="buyer_name" class="form-label">{{ __('sales.buyer_name') }}</label>
        <input id="buyer_name" type="text" name="buyer_name" value="{{ old('buyer_name', $record->buyer_name ?? '') }}" class="form-control @error('buyer_name') is-invalid @enderror" maxlength="150" required>
        @error('buyer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="buyer_phone" class="form-label">{{ __('sales.buyer_phone') }}</label>
        <input id="buyer_phone" type="text" name="buyer_phone" value="{{ old('buyer_phone', $record->buyer_phone ?? '') }}" class="form-control @error('buyer_phone') is-invalid @enderror" maxlength="50">
        @error('buyer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="birds_sold" class="form-label">{{ __('sales.birds_sold') }}</label>
        <input id="birds_sold" type="number" name="birds_sold" min="1" step="1" value="{{ old('birds_sold', $record->birds_sold ?? '') }}" class="form-control @error('birds_sold') is-invalid @enderror" required>
        @error('birds_sold')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="average_weight" class="form-label">{{ __('sales.average_weight') }}</label>
        <input id="average_weight" type="number" name="average_weight" min="0.001" max="99999" step="0.001" value="{{ old('average_weight', $record->average_weight ?? '') }}" class="form-control @error('average_weight') is-invalid @enderror">
        @error('average_weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="total_weight" class="form-label">{{ __('sales.total_weight') }}</label>
        <input id="total_weight" type="number" name="total_weight" min="0.001" max="9999999" step="0.001" value="{{ old('total_weight', $record->total_weight ?? '') }}" class="form-control @error('total_weight') is-invalid @enderror" required>
        @error('total_weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="rate_per_kg" class="form-label">{{ __('sales.rate_per_kg') }}</label>
        <input id="rate_per_kg" type="number" name="rate_per_kg" min="0.01" max="999999" step="0.01" value="{{ old('rate_per_kg', $record->rate_per_kg ?? '') }}" class="form-control @error('rate_per_kg') is-invalid @enderror" required>
        @error('rate_per_kg')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="total_amount" class="form-label">{{ __('sales.total_amount') }}</label>
        <input id="total_amount" type="number" name="total_amount" min="0.01" max="999999999" step="0.01" value="{{ old('total_amount', $record->total_amount ?? '') }}" class="form-control @error('total_amount') is-invalid @enderror" required>
        @error('total_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="paid_amount" class="form-label">{{ __('sales.paid_amount') }}</label>
        <input id="paid_amount" type="number" name="paid_amount" min="0" max="999999999" step="0.01" value="{{ old('paid_amount', $record->paid_amount ?? 0) }}" class="form-control @error('paid_amount') is-invalid @enderror">
        @error('paid_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="due_amount" class="form-label">{{ __('sales.due_amount') }}</label>
        <input id="due_amount" type="number" name="due_amount" min="0" max="999999999" step="0.01" value="{{ old('due_amount', $record->due_amount ?? 0) }}" class="form-control @error('due_amount') is-invalid @enderror" readonly>
        @error('due_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="reference_no" class="form-label">{{ __('sales.reference_no') }}</label>
        <input id="reference_no" type="text" name="reference_no" value="{{ old('reference_no', $record->reference_no ?? '') }}" class="form-control @error('reference_no') is-invalid @enderror" maxlength="100">
        @error('reference_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="notes" class="form-label">{{ __('sales.notes') }}</label>
        <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $record->notes ?? '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ isset($record) ? route('sales.show', $record) : route('sales.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const birdsSold = document.getElementById('birds_sold');
            const averageWeight = document.getElementById('average_weight');
            const totalWeight = document.getElementById('total_weight');
            const ratePerKg = document.getElementById('rate_per_kg');
            const totalAmount = document.getElementById('total_amount');
            const paidAmount = document.getElementById('paid_amount');
            const dueAmount = document.getElementById('due_amount');

            const update = () => {
                const birds = Number(birdsSold.value);
                const average = Number(averageWeight.value);
                const weight = Number(totalWeight.value);
                const rate = Number(ratePerKg.value);
                const paid = Number(paidAmount.value);

                if (birds > 0 && average > 0 && !totalWeight.dataset.manual) {
                    totalWeight.value = (birds * average).toFixed(3);
                }

                const currentWeight = Number(totalWeight.value || weight);
                if (currentWeight > 0 && rate > 0 && !totalAmount.dataset.manual) {
                    totalAmount.value = (currentWeight * rate).toFixed(2);
                }

                const total = Number(totalAmount.value);
                dueAmount.value = Math.max(0, total - paid).toFixed(2);
            };

            totalWeight.addEventListener('input', () => {
                totalWeight.dataset.manual = totalWeight.value ? '1' : '';
                update();
            });
            totalAmount.addEventListener('input', () => {
                totalAmount.dataset.manual = totalAmount.value ? '1' : '';
                update();
            });
            [birdsSold, averageWeight, ratePerKg, paidAmount].forEach((input) => input.addEventListener('input', update));
        });
    </script>
@endpush
