<?php

namespace App\Http\Requests\FarmVariety;

use App\Models\FarmCategory;
use App\Models\FarmVariety;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreFarmVarietyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('farm-varieties.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $name = $this->input('name_en') ?: $this->input('name_bn') ?: '';
        $slug = $this->filled('slug')
            ? Str::slug($this->input('slug'))
            : $this->uniqueSlug(Str::slug($name) ?: 'variety');

        $this->merge([
            'slug' => $slug,
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }

    public function rules(): array
    {
        return [
            'farm_category_id' => ['required', 'integer', Rule::exists('farm_categories', 'id')],
            'name_en' => ['required_without:name_bn', 'nullable', 'string', 'max:120'],
            'name_bn' => ['required_without:name_en', 'nullable', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:150', Rule::unique('farm_varieties', 'slug')],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('farm_varieties', 'code')->where('farm_category_id', $this->input('farm_category_id')),
            ],
            'description_en' => ['nullable', 'string'],
            'description_bn' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'farm_category_id' => __('farm_varieties.category'),
            'name_en' => __('farm_varieties.english_name'),
            'name_bn' => __('farm_varieties.bengali_name'),
            'slug' => __('farm_varieties.slug'),
            'code' => __('farm_varieties.code'),
            'description_en' => __('farm_varieties.english_description'),
            'description_bn' => __('farm_varieties.bengali_description'),
            'sort_order' => __('farm_varieties.sort_order'),
            'is_active' => __('farm_varieties.status'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $category = $this->category();

            if ($category && $category->isTopLevel()) {
                $validator->errors()->add('farm_category_id', __('farm_varieties.child_category_required'));
            }
        });
    }

    private function category(): ?FarmCategory
    {
        return $this->input('farm_category_id') ? FarmCategory::find($this->input('farm_category_id')) : null;
    }

    private function uniqueSlug(string $slug): string
    {
        $base = Str::limit($slug, 140, '');
        $candidate = $base;
        $counter = 2;

        while (FarmVariety::where('slug', $candidate)->exists()) {
            $candidate = Str::limit($base, 140 - strlen((string) $counter), '').'-'.$counter;
            $counter++;
        }

        return $candidate;
    }
}
