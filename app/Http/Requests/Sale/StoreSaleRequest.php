<?php

namespace App\Http\Requests\Sale;

use App\Enums\PaymentMethod;
use App\Models\Batch;
use App\Services\BatchCalculationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('sales.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $birdsSold = $this->input('birds_sold');
        $averageWeight = $this->input('average_weight');
        $totalWeight = $this->input('total_weight');
        $ratePerKg = $this->input('rate_per_kg');
        $totalAmount = $this->input('total_amount');
        $paidAmount = $this->input('paid_amount', 0);

        if (($totalWeight === null || $totalWeight === '') && is_numeric($birdsSold) && is_numeric($averageWeight)) {
            $totalWeight = (int) $birdsSold * (float) $averageWeight;
        }

        if (($totalAmount === null || $totalAmount === '') && is_numeric($totalWeight) && is_numeric($ratePerKg)) {
            $totalAmount = (float) $totalWeight * (float) $ratePerKg;
        }

        $dueAmount = is_numeric($totalAmount) && is_numeric($paidAmount)
            ? max(0, (float) $totalAmount - (float) $paidAmount)
            : $this->input('due_amount', 0);

        $this->merge([
            'payment_method' => $this->input('payment_method', PaymentMethod::CASH),
            'total_weight' => $totalWeight,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'due_amount' => $dueAmount,
            'buyer_name' => $this->clean('buyer_name'),
            'buyer_phone' => $this->clean('buyer_phone'),
            'reference_no' => $this->clean('reference_no'),
            'notes' => $this->clean('notes'),
        ]);
    }

    public function rules(): array
    {
        return [
            'batch_id' => ['required', 'integer', Rule::exists('batches', 'id')->whereNull('deleted_at')],
            'sale_date' => ['required', 'date'],
            'buyer_name' => ['required', 'string', 'max:150'],
            'buyer_phone' => ['nullable', 'string', 'max:50'],
            'birds_sold' => ['required', 'integer', 'min:1'],
            'average_weight' => ['nullable', 'numeric', 'min:0.001', 'max:99999'],
            'total_weight' => ['required', 'numeric', 'min:0.001', 'max:9999999'],
            'rate_per_kg' => ['required', 'numeric', 'min:0.01', 'max:999999'],
            'total_amount' => ['required', 'numeric', 'min:0.01', 'max:999999999'],
            'payment_method' => ['required', Rule::in(PaymentMethod::values())],
            'paid_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'due_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'batch_id' => __('sales.batch'),
            'sale_date' => __('sales.sale_date'),
            'buyer_name' => __('sales.buyer_name'),
            'buyer_phone' => __('sales.buyer_phone'),
            'birds_sold' => __('sales.birds_sold'),
            'average_weight' => __('sales.average_weight'),
            'total_weight' => __('sales.total_weight'),
            'rate_per_kg' => __('sales.rate_per_kg'),
            'total_amount' => __('sales.total_amount'),
            'payment_method' => __('sales.payment_method'),
            'paid_amount' => __('sales.paid_amount'),
            'due_amount' => __('sales.due_amount'),
            'reference_no' => __('sales.reference_no'),
            'notes' => __('sales.notes'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $batch = Batch::find($this->input('batch_id'));

            if (! $batch) {
                return;
            }

            if ($this->input('sale_date') < $batch->purchase_date->format('Y-m-d')) {
                $validator->errors()->add('sale_date', __('sales.before_batch_purchase'));
            }

            $currentBirds = app(BatchCalculationService::class)->details($batch)['current_birds'] + $this->existingBirdsAllowance();

            if ((int) $this->input('birds_sold', 0) > $currentBirds) {
                $validator->errors()->add('birds_sold', __('sales.birds_exceed_current'));
            }

            if ((float) $this->input('paid_amount', 0) > (float) $this->input('total_amount', 0)) {
                $validator->errors()->add('paid_amount', __('sales.paid_exceeds_total'));
            }
        });
    }

    protected function existingBirdsAllowance(): int
    {
        return 0;
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
