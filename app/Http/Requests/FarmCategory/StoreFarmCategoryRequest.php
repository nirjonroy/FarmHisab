<?php

namespace App\Http\Requests\FarmCategory;

use App\Models\FarmCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreFarmCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('farm-categories.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $slug = $this->filled('slug')
            ? Str::slug($this->input('slug'))
            : $this->uniqueSlug(Str::slug($this->input('name', '')) ?: 'category');

        $this->merge([
            'slug' => $slug,
            'sort_order' => $this->input('sort_order', 0),
            'parent_id' => $this->filled('parent_id') ? $this->input('parent_id') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'integer', Rule::exists('farm_categories', 'id')],
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('farm_categories', 'slug')],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'parent_id' => __('farm_categories.parent_category'),
            'name' => __('farm_categories.category_name'),
            'slug' => __('farm_categories.slug'),
            'description' => __('farm_categories.description'),
            'icon' => __('farm_categories.icon'),
            'sort_order' => __('farm_categories.sort_order'),
            'is_active' => __('farm_categories.status'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $parent = $this->parentCategory();

            if ($parent && ! $parent->isTopLevel()) {
                $validator->errors()->add('parent_id', __('validation.custom.parent_id.top_level_parent'));
            }
        });
    }

    private function parentCategory(): ?FarmCategory
    {
        return $this->input('parent_id') ? FarmCategory::find($this->input('parent_id')) : null;
    }

    private function uniqueSlug(string $slug): string
    {
        $base = Str::limit($slug, 110, '');
        $candidate = $base;
        $counter = 2;

        while (FarmCategory::where('slug', $candidate)->exists()) {
            $candidate = Str::limit($base, 110 - strlen((string) $counter), '').'-'.$counter;
            $counter++;
        }

        return $candidate;
    }
}
