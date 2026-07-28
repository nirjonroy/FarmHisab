<?php

namespace App\Http\Requests\Expense;

use App\Enums\ExpenseCategory;
use App\Enums\PaymentMethod;
use App\Models\Batch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('expenses.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'batch_id' => $this->input('batch_id') ?: null,
            'category' => $this->input('category', ExpenseCategory::OTHER),
            'payment_method' => $this->input('payment_method', PaymentMethod::CASH),
            'title' => $this->clean('title'),
            'payee' => $this->clean('payee'),
            'reference_no' => $this->clean('reference_no'),
            'notes' => $this->clean('notes'),
        ]);
    }

    public function rules(): array
    {
        return [
            'batch_id' => ['nullable', 'integer', Rule::exists('batches', 'id')->whereNull('deleted_at')],
            'expense_date' => ['required', 'date'],
            'category' => ['required', Rule::in(ExpenseCategory::values())],
            'title' => ['required', 'string', 'max:150'],
            'payee' => ['nullable', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999'],
            'payment_method' => ['required', Rule::in(PaymentMethod::values())],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'batch_id' => __('expenses.batch'),
            'expense_date' => __('expenses.expense_date'),
            'category' => __('expenses.category'),
            'title' => __('expenses.expense_title'),
            'payee' => __('expenses.payee'),
            'amount' => __('expenses.amount'),
            'payment_method' => __('expenses.payment_method'),
            'reference_no' => __('expenses.reference_no'),
            'notes' => __('expenses.notes'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $batch = Batch::find($this->input('batch_id'));

            if ($batch && $this->input('expense_date') < $batch->purchase_date->format('Y-m-d')) {
                $validator->errors()->add('expense_date', __('expenses.before_batch_purchase'));
            }
        });
    }

    protected function clean(string $key): ?string
    {
        $value = $this->input($key);

        if ($value === null) {
            return null;
        }

        $value = trim(preg_replace('/\s+/u', ' ', (string) $value));

        return $value === '' ? null : $value;
    }
}
