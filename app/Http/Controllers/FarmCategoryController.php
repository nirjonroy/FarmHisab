<?php

namespace App\Http\Controllers;

use App\Http\Requests\FarmCategory\StoreFarmCategoryRequest;
use App\Http\Requests\FarmCategory\UpdateFarmCategoryRequest;
use App\Models\FarmCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FarmCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $parentId = $request->integer('parent_id') ?: null;
        $level = $request->string('level')->toString();
        $status = $request->string('status')->toString();

        $categories = FarmCategory::query()
            ->with(['parent', 'createdBy'])
            ->withCount('children')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($parentId, fn ($query) => $query->where('parent_id', $parentId))
            ->when($level === 'top-level', fn ($query) => $query->topLevel())
            ->when($level === 'child', fn ($query) => $query->whereNotNull('parent_id'))
            ->when(in_array($status, ['active', 'inactive'], true), function ($query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('farm-categories.index', [
            'categories' => $categories,
            'parentCategories' => FarmCategory::topLevel()->ordered()->get(),
            'search' => $search,
            'parentId' => $parentId,
            'level' => $level,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('farm-categories.create', [
            'parentCategories' => FarmCategory::active()->topLevel()->ordered()->get(),
        ]);
    }

    public function store(StoreFarmCategoryRequest $request): RedirectResponse
    {
        FarmCategory::create($this->payload($request->validated()) + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('farm-categories.index')->with('success', __('messages.farm_category_created'));
    }

    public function edit(FarmCategory $farmCategory): View
    {
        $parentCategories = FarmCategory::query()
            ->topLevel()
            ->whereKeyNot($farmCategory->id)
            ->where(function ($query) use ($farmCategory) {
                $query->where('is_active', true)
                    ->orWhereKey($farmCategory->parent_id);
            })
            ->ordered()
            ->get();

        return view('farm-categories.edit', compact('farmCategory', 'parentCategories'));
    }

    public function update(UpdateFarmCategoryRequest $request, FarmCategory $farmCategory): RedirectResponse
    {
        $farmCategory->update($this->payload($request->validated()));

        return redirect()->route('farm-categories.index')->with('success', __('messages.farm_category_updated'));
    }

    private function payload(array $data): array
    {
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
