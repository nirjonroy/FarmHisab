<?php

namespace App\Http\Requests\FeedRecord;

use App\Models\Batch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreFeedRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ($this->user()?->can('feed.manage') || $this->user()?->can('feed-usage.create')) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'feed_name' => $this->clean('feed_name'),
            'supplier_name' => $this->clean('supplier_name'),
            'notes' => $this->clean('notes'),
            'bags' => $this->input('bags', 0),
            'weight_per_bag' => $this->input('weight_per_bag', 50),
            'unit_price_per_bag' => $this->input('unit_price_per_bag', 0),
        ]);
    }

    public function rules(): array
    {
        return [
            'batch_id' => ['required', 'integer', Rule::exists('batches', 'id')->whereNull('deleted_at')],
            'product_id' => ['nullable', 'integer', Rule::exists('products', 'id')],
            'record_date' => ['required', 'date'],
            'feed_name' => ['nullable', 'string', 'max:150'],
            'supplier_name' => ['nullable', 'string', 'max:150'],
            'bags' => ['required', 'numeric', 'min:0.01'],
            'weight_per_bag' => ['required', 'numeric', 'min:0.01'],
            'unit_price_per_bag' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'batch_id' => __('feed.batch'),
            'product_id' => __('feed.product'),
            'record_date' => __('feed.record_date'),
            'feed_name' => __('feed.feed_name'),
            'supplier_name' => __('feed.supplier_name'),
            'bags' => __('feed.bags'),
            'weight_per_bag' => __('feed.weight_per_bag'),
            'unit_price_per_bag' => __('feed.unit_price_per_bag'),
            'notes' => __('feed.notes'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $batch = Batch::find($this->input('batch_id'));

            if ($batch && $this->input('record_date') < $batch->purchase_date->format('Y-m-d')) {
                $validator->errors()->add('record_date', __('feed.before_batch_purchase'));
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
