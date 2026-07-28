<?php

namespace App\Http\Requests\Inventory;

use App\Enums\InventoryMovementType;
use App\Models\Batch;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreInventoryMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('inventory.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $quantity = $this->input('quantity');
        $unitCost = $this->input('unit_cost', 0);
        $totalCost = $this->input('total_cost');

        if (($totalCost === null || $totalCost === '') && is_numeric($quantity) && is_numeric($unitCost)) {
            $totalCost = (float) $quantity * (float) $unitCost;
        }

        $this->merge([
            'batch_id' => $this->input('batch_id') ?: null,
            'type' => $this->input('type', InventoryMovementType::PURCHASE),
            'unit_cost' => $unitCost,
            'total_cost' => $totalCost,
            'supplier_name' => $this->clean('supplier_name'),
            'reference_no' => $this->clean('reference_no'),
            'notes' => $this->clean('notes'),
        ]);
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where('is_active', true)->where('is_stock_tracked', true),
            ],
            'batch_id' => ['nullable', 'integer', Rule::exists('batches', 'id')->whereNull('deleted_at')],
            'movement_date' => ['required', 'date'],
            'type' => ['required', Rule::in(InventoryMovementType::values())],
            'quantity' => ['required', 'numeric', 'min:0.001', 'max:999999999'],
            'unit_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'total_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'supplier_name' => ['nullable', 'string', 'max:150'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'product_id' => __('inventory.product'),
            'batch_id' => __('inventory.batch'),
            'movement_date' => __('inventory.movement_date'),
            'type' => __('inventory.type'),
            'quantity' => __('inventory.quantity'),
            'unit_cost' => __('inventory.unit_cost'),
            'total_cost' => __('inventory.total_cost'),
            'supplier_name' => __('inventory.supplier_name'),
            'reference_no' => __('inventory.reference_no'),
            'notes' => __('inventory.notes'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $batch = Batch::find($this->input('batch_id'));

            if ($batch && $this->input('movement_date') < $batch->purchase_date->format('Y-m-d')) {
                $validator->errors()->add('movement_date', __('inventory.before_batch_purchase'));
            }

            $product = Product::find($this->input('product_id'));

            if (! $product) {
                return;
            }

            if (in_array($this->input('type'), InventoryMovementType::outboundValues(), true)) {
                $currentStock = app(InventoryService::class)->currentStock($product->id, $this->ignoreMovementId());

                if ((float) $this->input('quantity', 0) > $currentStock) {
                    $validator->errors()->add('quantity', __('inventory.quantity_exceeds_stock'));
                }
            }
        });
    }

    protected function ignoreMovementId(): ?int
    {
        return null;
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
