<?php

namespace App\Http\Requests\FarmCategory;

use App\Enums\CategoryActivityType;
use App\Models\FarmCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateFarmCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('farm-categories.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $category = $this->route('farmCategory');
        $nameEn = $this->input('name_en') ?: $this->input('name');
        $nameBn = $this->input('name_bn');
        $legacyName = $nameEn ?: $nameBn ?: $this->input('name');
        $slug = $this->filled('slug')
            ? Str::slug($this->input('slug'))
            : $this->uniqueSlug(Str::slug($legacyName ?: '') ?: 'category', $category?->id);

        $this->merge([
            'name_en' => $nameEn,
            'name_bn' => $nameBn,
            'name' => $legacyName,
            'slug' => $slug,
            'activity_type' => $this->input('activity_type', $category?->activity_type?->value ?? CategoryActivityType::PRODUCTION),
            'sort_order' => $this->input('sort_order', 0),
            'parent_id' => $this->filled('parent_id') ? $this->input('parent_id') : null,
        ]);
    }

    public function rules(): array
    {
        $category = $this->route('farmCategory');

        return [
            'parent_id' => ['nullable', 'integer', Rule::exists('farm_categories', 'id')],
            'name_en' => ['required_without:name_bn', 'nullable', 'string', 'max:100'],
            'name_bn' => ['required_without:name_en', 'nullable', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('farm_categories', 'slug')->ignore($category)],
            'description_en' => ['nullable', 'string'],
            'description_bn' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'activity_type' => ['required', Rule::in(CategoryActivityType::values())],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'parent_id' => __('farm_categories.parent_category'),
            'name' => __('farm_categories.category_name'),
            'name_en' => __('farm_categories.english_name'),
            'name_bn' => __('farm_categories.bengali_name'),
            'slug' => __('farm_categories.slug'),
            'description' => __('farm_categories.description'),
            'description_en' => __('farm_categories.english_description'),
            'description_bn' => __('farm_categories.bengali_description'),
            'icon' => __('farm_categories.icon'),
            'activity_type' => __('farm_categories.activity_type'),
            'sort_order' => __('farm_categories.sort_order'),
            'is_active' => __('farm_categories.status'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $category = $this->route('farmCategory');
            $parent = $this->parentCategory();

            if (! $category instanceof FarmCategory) {
                return;
            }

            if ($parent && $parent->is($category)) {
                $validator->errors()->add('parent_id', __('validation.custom.parent_id.self_parent'));
            }

            if ($parent && ! $parent->isTopLevel()) {
                $validator->errors()->add('parent_id', __('validation.custom.parent_id.top_level_parent'));
            }

            if ($parent && $category->hasChildren()) {
                $validator->errors()->add('parent_id', __('validation.custom.parent_id.parent_with_children'));
            }
        });
    }

    private function parentCategory(): ?FarmCategory
    {
        return $this->input('parent_id') ? FarmCategory::find($this->input('parent_id')) : null;
    }

    private function uniqueSlug(string $slug, ?int $ignoreId): string
    {
        $base = Str::limit($slug, 110, '');
        $candidate = $base;
        $counter = 2;

        while (FarmCategory::where('slug', $candidate)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $candidate = Str::limit($base, 110 - strlen((string) $counter), '').'-'.$counter;
            $counter++;
        }

        return $candidate;
    }
}
