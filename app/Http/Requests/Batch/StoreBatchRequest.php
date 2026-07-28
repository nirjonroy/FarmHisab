<?php

namespace App\Http\Requests\Batch;

use App\Enums\BatchStatus;
use App\Models\FarmCategory;
use App\Models\FarmVariety;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('batches.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $initialBirds = (int) $this->input('initial_birds', 0);
        $price = (float) $this->input('purchase_price_per_bird', 0);

        $this->merge([
            'batch_name' => $this->clean('batch_name'),
            'supplier_name' => $this->clean('supplier_name'),
            'notes' => $this->clean('notes'),
            'status' => $this->input('status', BatchStatus::ACTIVE),
            'total_purchase_cost' => round($initialBirds * $price, 2),
        ]);
    }

    public function rules(): array
    {
        return [
            'batch_name' => ['required', 'string', 'max:150'],
            'farm_id' => ['nullable', 'integer', Rule::exists('farms', 'id')],
            'bird_type_id' => ['required', 'integer', Rule::exists('farm_categories', 'id')],
            'breed_id' => ['required', 'integer', Rule::exists('farm_varieties', 'id')],
            'supplier_name' => ['nullable', 'string', 'max:150'],
            'purchase_date' => ['required', 'date'],
            'arrival_date' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'initial_birds' => ['required', 'integer', 'min:1'],
            'purchase_price_per_bird' => ['required', 'numeric', 'min:0'],
            'total_purchase_cost' => ['required', 'numeric', 'min:0'],
            'expected_market_weight' => ['nullable', 'numeric', 'min:0'],
            'expected_market_age' => ['nullable', 'integer', 'min:0'],
            'feed_target_bags' => ['nullable', 'numeric', 'min:0'],
            'medicine_budget' => ['nullable', 'numeric', 'min:0'],
            'other_budget' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(BatchStatus::values())],
        ];
    }

    public function attributes(): array
    {
        return [
            'batch_name' => __('batches.batch_name'),
            'farm_id' => __('batches.farm'),
            'bird_type_id' => __('batches.bird_type'),
            'breed_id' => __('batches.breed'),
            'supplier_name' => __('batches.supplier_name'),
            'purchase_date' => __('batches.purchase_date'),
            'arrival_date' => __('batches.arrival_date'),
            'initial_birds' => __('batches.initial_birds'),
            'purchase_price_per_bird' => __('batches.purchase_price_per_bird'),
            'expected_market_weight' => __('batches.expected_market_weight'),
            'expected_market_age' => __('batches.expected_market_age'),
            'feed_target_bags' => __('batches.feed_target_bags'),
            'medicine_budget' => __('batches.medicine_budget'),
            'other_budget' => __('batches.other_budget'),
            'notes' => __('batches.notes'),
            'status' => __('batches.status'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $birdType = FarmCategory::find($this->input('bird_type_id'));
            $breed = FarmVariety::find($this->input('breed_id'));

            if ($birdType && $birdType->isTopLevel()) {
                $validator->errors()->add('bird_type_id', __('batches.child_category_required'));
            }

            if ($breed && $birdType && (int) $breed->farm_category_id !== (int) $birdType->id) {
                $validator->errors()->add('breed_id', __('batches.breed_category_mismatch'));
            }
        });
    }

    private function clean(string $key): ?string
    {
        $value = $this->input($key);

        if ($value === null) {
            return null;
        }

        $value = trim(preg_replace('/\s+/u', ' ', (string) $value));

        return $value === '' ? null : $value;
    }
}
