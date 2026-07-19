<?php

namespace App\Http\Requests\FarmCategory;

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
        $slug = $this->filled('slug')
            ? Str::slug($this->input('slug'))
            : $this->uniqueSlug(Str::slug($this->input('name', '')) ?: 'category', $category?->id);

        $this->merge([
            'slug' => $slug,
            'sort_order' => $this->input('sort_order', 0),
            'parent_id' => $this->filled('parent_id') ? $this->input('parent_id') : null,
        ]);
    }

    public function rules(): array
    {
        $category = $this->route('farmCategory');

        return [
            'parent_id' => ['nullable', 'integer', Rule::exists('farm_categories', 'id')],
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('farm_categories', 'slug')->ignore($category)],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
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
                $validator->errors()->add('parent_id', 'A category cannot be its own parent.');
            }

            if ($parent && ! $parent->isTopLevel()) {
                $validator->errors()->add('parent_id', 'Only top-level categories can be selected as a parent.');
            }

            if ($parent && $category->hasChildren()) {
                $validator->errors()->add('parent_id', 'A category with child categories cannot be moved under another parent.');
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
