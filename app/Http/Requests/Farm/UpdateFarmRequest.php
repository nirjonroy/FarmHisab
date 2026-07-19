<?php

namespace App\Http\Requests\Farm;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFarmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('farms.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', Rule::unique('farms', 'code')->ignore($this->route('farm'))],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'district' => ['nullable', 'string', 'max:100'],
            'upazila' => ['nullable', 'string', 'max:100'],
            'union_name' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('farms.farm_name'),
            'code' => __('farms.farm_code'),
            'phone' => __('farms.phone'),
            'address' => __('farms.address'),
            'district' => __('farms.district'),
            'upazila' => __('farms.upazila'),
            'union_name' => __('farms.union'),
            'description' => __('farms.description'),
            'is_active' => __('farms.status'),
        ];
    }
}
