<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductUsageType;
use App\Models\FarmCategory;
use App\Models\MeasurementUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('products.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name_en' => $this->clean('name_en'),
            'name_bn' => $this->clean('name_bn'),
            'sku' => Str::upper($this->clean('sku') ?? ''),
            'barcode' => $this->clean('barcode'),
            'usage_type' => $this->input('usage_type', ProductUsageType::BOTH),
            'description_en' => $this->clean('description_en'),
            'description_bn' => $this->clean('description_bn'),
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }

    public function rules(): array
    {
        return [
            'farm_category_id' => ['required', 'integer', Rule::exists('farm_categories', 'id')],
            'measurement_unit_id' => ['required', 'integer', Rule::exists('measurement_units', 'id')],
            'name_en' => ['required_without:name_bn', 'nullable', 'string', 'max:150'],
            'name_bn' => ['required_without:name_en', 'nullable', 'string', 'max:150'],
            'sku' => ['required', 'string', 'max:60', Rule::unique('products', 'sku')],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('products', 'barcode')],
            'usage_type' => ['required', Rule::in(ProductUsageType::values())],
            'description_en' => ['nullable', 'string'],
            'description_bn' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_stock_tracked' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'farm_category_id' => __('products.category'),
            'measurement_unit_id' => __('products.measurement_unit'),
            'name_en' => __('products.english_name'),
            'name_bn' => __('products.bengali_name'),
            'sku' => __('products.sku'),
            'barcode' => __('products.barcode'),
            'usage_type' => __('products.usage_type'),
            'description_en' => __('products.english_description'),
            'description_bn' => __('products.bengali_description'),
            'sort_order' => __('products.sort_order'),
            'is_stock_tracked' => __('products.stock_tracked'),
            'is_active' => __('products.status'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $category = FarmCategory::find($this->input('farm_category_id'));
            $unit = MeasurementUnit::find($this->input('measurement_unit_id'));

            if ($category && $category->isTopLevel()) {
                $validator->errors()->add('farm_category_id', __('products.child_category_required'));
            }

            if ($unit && ! $unit->is_active) {
                $validator->errors()->add('measurement_unit_id', __('products.active_unit_required'));
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
