@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="batch_id" class="form-label">{{ __('expenses.batch') }}</label>
        <select id="batch_id" name="batch_id" class="form-select @error('batch_id') is-invalid @enderror">
            <option value="">{{ __('common.not_set') }}</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->id }}" @selected((int) old('batch_id', $record->batch_id ?? request('batch_id')) === $batch->id)>{{ $batch->batch_no }} - {{ $batch->batch_name }}</option>
            @endforeach
        </select>
        @error('batch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="expense_date" class="form-label">{{ __('expenses.expense_date') }}</label>
        <input id="expense_date" type="date" name="expense_date" value="{{ old('expense_date', isset($record) ? $record->expense_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" class="form-control @error('expense_date') is-invalid @enderror" required>
        @error('expense_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="category" class="form-label">{{ __('expenses.category') }}</label>
        <select id="category" name="category" class="form-select @error('category') is-invalid @enderror" required>
            @foreach ($categories as $value => $label)
                <option value="{{ $value }}" @selected(old('category', $record->category->value ?? 'other') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="title" class="form-label">{{ __('expenses.expense_title') }}</label>
        <input id="title" type="text" name="title" value="{{ old('title', $record->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" maxlength="150" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="amount" class="form-label">{{ __('expenses.amount') }}</label>
        <input id="amount" type="number" name="amount" min="0.01" max="999999999" step="0.01" value="{{ old('amount', $record->amount ?? '') }}" class="form-control @error('amount') is-invalid @enderror" required>
        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="payment_method" class="form-label">{{ __('expenses.payment_method') }}</label>
        <select id="payment_method" name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
            @foreach ($paymentMethods as $value => $label)
                <option value="{{ $value }}" @selected(old('payment_method', $record->payment_method->value ?? 'cash') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="payee" class="form-label">{{ __('expenses.payee') }}</label>
        <input id="payee" type="text" name="payee" value="{{ old('payee', $record->payee ?? '') }}" class="form-control @error('payee') is-invalid @enderror" maxlength="150">
        @error('payee')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="reference_no" class="form-label">{{ __('expenses.reference_no') }}</label>
        <input id="reference_no" type="text" name="reference_no" value="{{ old('reference_no', $record->reference_no ?? '') }}" class="form-control @error('reference_no') is-invalid @enderror" maxlength="100">
        @error('reference_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="notes" class="form-label">{{ __('expenses.notes') }}</label>
        <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $record->notes ?? '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ isset($record) ? route('expenses.show', $record) : route('expenses.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
</div>
