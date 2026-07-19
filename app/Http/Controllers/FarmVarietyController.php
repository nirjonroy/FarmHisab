<?php

namespace App\Http\Controllers;

use App\Http\Requests\FarmVariety\StoreFarmVarietyRequest;
use App\Http\Requests\FarmVariety\UpdateFarmVarietyRequest;
use App\Models\FarmCategory;
use App\Models\FarmVariety;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FarmVarietyController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $categoryId = $request->integer('farm_category_id') ?: null;
        $parentId = $request->integer('parent_id') ?: null;
        $status = $request->string('status')->toString();

        $varieties = FarmVariety::query()
            ->with(['category.parent', 'createdBy'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_bn', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn ($query) => $query->where('farm_category_id', $categoryId))
            ->when($parentId, function ($query) use ($parentId) {
                $query->whereHas('category', fn ($query) => $query->where('parent_id', $parentId));
            })
            ->when(in_array($status, ['active', 'inactive'], true), function ($query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('farm-varieties.index', [
            'varieties' => $varieties,
            'categories' => $this->childCategories(),
            'parentCategories' => FarmCategory::topLevel()->ordered()->get(),
            'search' => $search,
            'categoryId' => $categoryId,
            'parentId' => $parentId,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('farm-varieties.create', [
            'categories' => $this->childCategories(activeOnly: true),
        ]);
    }

    public function store(StoreFarmVarietyRequest $request): RedirectResponse
    {
        FarmVariety::create($this->payload($request->validated()) + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('farm-varieties.index')->with('success', __('farm_varieties.create_success'));
    }

    public function edit(FarmVariety $farmVariety): View
    {
        $categories = FarmCategory::query()
            ->with('parent')
            ->whereNotNull('parent_id')
            ->where(function ($query) use ($farmVariety) {
                $query->where('is_active', true)
                    ->orWhere('id', $farmVariety->farm_category_id);
            })
            ->ordered()
            ->get();

        return view('farm-varieties.edit', compact('farmVariety', 'categories'));
    }

    public function update(UpdateFarmVarietyRequest $request, FarmVariety $farmVariety): RedirectResponse
    {
        $farmVariety->update($this->payload($request->validated()));

        return redirect()->route('farm-varieties.index')->with('success', __('farm_varieties.update_success'));
    }

    private function childCategories(bool $activeOnly = false)
    {
        return FarmCategory::query()
            ->with('parent')
            ->whereNotNull('parent_id')
            ->when($activeOnly, fn ($query) => $query->where('is_active', true))
            ->ordered()
            ->get();
    }

    private function payload(array $data): array
    {
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
