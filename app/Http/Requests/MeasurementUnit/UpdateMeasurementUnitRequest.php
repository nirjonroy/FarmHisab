<?php

namespace App\Http\Requests\MeasurementUnit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateMeasurementUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('measurement-units.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name_en' => $this->clean('name_en'),
            'name_bn' => $this->clean('name_bn'),
            'short_name_en' => $this->clean('short_name_en'),
            'short_name_bn' => $this->clean('short_name_bn'),
            'code' => Str::lower($this->clean('code') ?? ''),
            'description_en' => $this->clean('description_en'),
            'description_bn' => $this->clean('description_bn'),
            'decimal_places' => $this->input('decimal_places', 2),
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }

    public function rules(): array
    {
        $measurementUnit = $this->route('measurementUnit');

        return [
            'name_en' => ['required_without:name_bn', 'nullable', 'string', 'max:100'],
            'name_bn' => ['required_without:name_en', 'nullable', 'string', 'max:100'],
            'short_name_en' => ['required_without:short_name_bn', 'nullable', 'string', 'max:30'],
            'short_name_bn' => ['required_without:short_name_en', 'nullable', 'string', 'max:30'],
            'code' => ['required', 'string', 'max:30', Rule::unique('measurement_units', 'code')->ignore($measurementUnit)],
            'description_en' => ['nullable', 'string'],
            'description_bn' => ['nullable', 'string'],
            'decimal_places' => ['required', 'integer', 'min:0', 'max:4'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name_en' => __('measurement_units.english_name'),
            'name_bn' => __('measurement_units.bengali_name'),
            'short_name_en' => __('measurement_units.english_short_name'),
            'short_name_bn' => __('measurement_units.bengali_short_name'),
            'code' => __('measurement_units.code'),
            'description_en' => __('measurement_units.english_description'),
            'description_bn' => __('measurement_units.bengali_description'),
            'decimal_places' => __('measurement_units.decimal_places'),
            'sort_order' => __('measurement_units.sort_order'),
            'is_active' => __('measurement_units.status'),
        ];
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
