<?php

namespace App\Http\Requests\Shed;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('farms.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')],
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('sheds', 'code')
                    ->where('farm_id', $this->input('farm_id'))
                    ->ignore($this->route('shed')),
            ],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
