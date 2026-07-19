<?php

namespace App\Http\Controllers;

use App\Enums\ProductUsageType;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\FarmCategory;
use App\Models\MeasurementUnit;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $parentId = $request->integer('parent_id') ?: null;
        $categoryId = $request->integer('farm_category_id') ?: null;
        $unitId = $request->integer('measurement_unit_id') ?: null;
        $usageType = $request->string('usage_type')->toString();
        $status = $request->string('status')->toString();
        $stockTracked = $request->string('stock_tracked')->toString();

        $products = Product::query()
            ->with(['category.parent', 'unit', 'createdBy'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_bn', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when($parentId, function ($query) use ($parentId) {
                $query->whereHas('category', fn ($query) => $query->where('parent_id', $parentId));
            })
            ->when($categoryId, fn ($query) => $query->where('farm_category_id', $categoryId))
            ->when($unitId, fn ($query) => $query->where('measurement_unit_id', $unitId))
            ->when(ProductUsageType::tryFrom($usageType), fn ($query) => $query->where('usage_type', $usageType))
            ->when(in_array($status, ['active', 'inactive'], true), function ($query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->when(in_array($stockTracked, ['tracked', 'not-tracked'], true), function ($query) use ($stockTracked) {
                $query->where('is_stock_tracked', $stockTracked === 'tracked');
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'parentCategories' => FarmCategory::topLevel()->ordered()->get(),
            'categories' => $this->childCategories(),
            'units' => MeasurementUnit::ordered()->get(),
            'usageTypes' => ProductUsageType::options(),
            'search' => $search,
            'parentId' => $parentId,
            'categoryId' => $categoryId,
            'unitId' => $unitId,
            'usageType' => $usageType,
            'status' => $status,
            'stockTracked' => $stockTracked,
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'categories' => $this->childCategories(activeOnly: true),
            'units' => MeasurementUnit::active()->ordered()->get(),
            'usageTypes' => ProductUsageType::options(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create($this->payload($request->validated()) + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('products.index')->with('success', __('products.create_success'));
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => $this->childCategories(activeOnly: true, selectedId: $product->farm_category_id),
            'units' => MeasurementUnit::query()
                ->where('is_active', true)
                ->orWhere('id', $product->measurement_unit_id)
                ->ordered()
                ->get(),
            'usageTypes' => ProductUsageType::options(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($this->payload($request->validated()));

        return redirect()->route('products.index')->with('success', __('products.update_success'));
    }

    private function childCategories(bool $activeOnly = false, ?int $selectedId = null)
    {
        return FarmCategory::query()
            ->with('parent')
            ->whereNotNull('parent_id')
            ->when($activeOnly, function ($query) use ($selectedId) {
                $query->where(function ($query) use ($selectedId) {
                    $query->where('is_active', true)
                        ->when($selectedId, fn ($query) => $query->orWhere('id', $selectedId));
                });
            })
            ->ordered()
            ->get();
    }

    private function payload(array $data): array
    {
        $data['is_stock_tracked'] = (bool) ($data['is_stock_tracked'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
